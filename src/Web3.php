<?php

/**
 * 此文件是 web3.php 包的一部分。
 *
 * (c) Kuan-Cheng,Lai <alk03073135@gmail.com>
 *
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */

namespace Tourze\Web3PHP;

use Tourze\Web3PHP\Exception\InvalidArgumentException;
use Tourze\Web3PHP\Exception\RuntimeException;
use Tourze\Web3PHP\Providers\HttpProvider;
use Tourze\Web3PHP\Providers\IProvider;
use Tourze\Web3PHP\Providers\Provider;
use Tourze\Web3PHP\RequestManagers\HttpRequestManager;

/**
 * @property Eth $eth
 * @property Net $net
 * @property Personal $personal
 * @property Shh $shh
 * @property Utils $utils
 */
class Web3
{
    /**
     * 提供者
     *
     * @var IProvider
     */
    protected $provider;

    /**
     * 以太坊实例
     *
     * @var Eth|null
     */
    protected $eth;

    /**
     * 网络实例
     *
     * @var Net|null
     */
    protected $net;

    /**
     * 个人账户实例
     *
     * @var Personal|null
     */
    protected $personal;

    /**
     * 耳语协议实例
     *
     * @var Shh|null
     */
    protected $shh;

    /**
     * 工具类实例
     *
     * @var Utils|null
     */
    protected $utils;

    /**
     * 方法集合
     *
     * @var array<string, object>
     */
    private $methods = [];

    /**
     * 允许的方法
     *
     * @var array<int, string>
     */
    private $allowedMethods = [
        'web3_clientVersion', 'web3_sha3',
    ];

    /**
     * 构造函数
     *
     * @param string|Provider $provider
     */
    public function __construct($provider)
    {
        if (is_string($provider) && (false !== filter_var($provider, FILTER_VALIDATE_URL))) {
            // check the uri schema
            if (1 === preg_match('/^https?:\/\//', $provider)) {
                $requestManager = new HttpRequestManager($provider);

                $this->provider = new HttpProvider($requestManager);
            } else {
                throw new InvalidArgumentException('Invalid URL schema. Only http and https are supported.');
            }
        } elseif ($provider instanceof IProvider) {
            $this->provider = $provider;
        } else {
            throw new InvalidArgumentException('Provider must be a valid URL string or an instance of IProvider.');
        }
    }

    /**
     * 魔术方法调用
     *
     * @param string $name
     * @param array<int, mixed>  $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (!$this->isValidMethodName($name)) {
            return null;
        }

        $method = $this->buildMethodName($name);
        $this->validateMethod($method);

        $callbackResult = $this->extractCallback($arguments);
        $callback = $callbackResult['callback'];
        $arguments = $callbackResult['arguments'];
        $methodObject = $this->getMethodObject($method, $name, $arguments);

        $this->executeMethod($methodObject, $arguments, $callback);

        return null;
    }

    /**
     * 验证方法名是否有效
     */
    private function isValidMethodName(string $name): bool
    {
        return 1 === preg_match('/^[a-zA-Z0-9]+$/', $name);
    }

    /**
     * 构建方法名
     */
    private function buildMethodName(string $name): string
    {
        $class = explode('\\', get_class());

        return strtolower($class[1]) . '_' . $name;
    }

    /**
     * 验证方法是否允许
     */
    private function validateMethod(string $method): void
    {
        if (!in_array($method, $this->allowedMethods, true)) {
            throw new RuntimeException('Unallowed rpc method: ' . $method);
        }
    }

    /**
     * 提取回调函数
     * @param array<int, mixed> $arguments
     * @return array{callback: callable|null, arguments: array<int, mixed>}
     */
    private function extractCallback(array $arguments): array
    {
        if ($this->provider->getIsBatch()) {
            return ['callback' => null, 'arguments' => $arguments];
        }

        $callback = array_pop($arguments);
        if (true !== is_callable($callback)) {
            throw new InvalidArgumentException('The last param must be callback function.');
        }

        return ['callback' => $callback, 'arguments' => $arguments];
    }

    /**
     * 获取方法对象
     * @param array<int, mixed> $arguments
     */
    private function getMethodObject(string $method, string $name, array $arguments): object
    {
        if (!array_key_exists($method, $this->methods)) {
            $this->methods[$method] = $this->createMethodObject($method, $name, $arguments);
        }

        return $this->methods[$method];
    }

    /**
     * 创建方法对象
     * @param array<int, mixed> $arguments
     */
    private function createMethodObject(string $method, string $name, array $arguments): object
    {
        $class = explode('\\', get_class());
        $methodClass = sprintf('\Tourze\Web3PHP\Methods\%s\%s', ucfirst($class[1]), ucfirst($name));

        if (!class_exists($methodClass)) {
            throw new RuntimeException("Method class {$methodClass} does not exist.");
        }

        return new $methodClass($method, $arguments);
    }

    /**
     * 执行方法
     * @param array<int, mixed> $arguments
     */
    private function executeMethod(object $methodObject, array $arguments, ?callable $callback): void
    {
        if (!method_exists($methodObject, 'validate')
            || !method_exists($methodObject, 'transform')
            || !property_exists($methodObject, 'inputFormatters')
            || !property_exists($methodObject, 'arguments')) {
            throw new RuntimeException('Method object must implement required methods and properties.');
        }

        if ($methodObject->validate($arguments)) {
            $inputs = $methodObject->transform($arguments, $methodObject->inputFormatters);
            $methodObject->arguments = $inputs;
            $this->provider->send($methodObject, $callback);
        }
    }

    /**
     * 获取属性
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        $method = 'get' . ucfirst($name);

        if (method_exists($this, $method)) {
            /** @var callable $callable */
            $callable = [$this, $method];

            return call_user_func_array($callable, []);
        }

        return false;
    }

    /**
     * 设置属性
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value): void
    {
        $method = 'set' . ucfirst($name);

        if (method_exists($this, $method)) {
            /** @var callable $callable */
            $callable = [$this, $method];
            call_user_func_array($callable, [$value]);
        }
    }

    /**
     * 获取提供者
     *
     * @return IProvider
     */
    public function getProvider(): IProvider
    {
        return $this->provider;
    }

    /**
     * 设置提供者
     *
     * @param Provider $provider
     */
    public function setProvider($provider): void
    {
        if ($provider instanceof Provider) {
            $this->provider = $provider;

            return;
        }

        throw new \InvalidArgumentException('Provider must be an instance of Provider');
    }

    /**
     * 获取以太坊实例
     *
     * @return Eth
     */
    public function getEth(): Eth
    {
        if (!isset($this->eth)) {
            $eth = new Eth($this->provider);
            $this->eth = $eth;
        }

        return $this->eth;
    }

    /**
     * 获取网络实例
     *
     * @return Net
     */
    public function getNet(): Net
    {
        if (!isset($this->net)) {
            $net = new Net($this->provider);
            $this->net = $net;
        }

        return $this->net;
    }

    /**
     * 获取个人账户实例
     *
     * @return Personal
     */
    public function getPersonal(): Personal
    {
        if (!isset($this->personal)) {
            $personal = new Personal($this->provider);
            $this->personal = $personal;
        }

        return $this->personal;
    }

    /**
     * 获取耳语协议实例
     *
     * @return Shh
     */
    public function getShh(): Shh
    {
        if (!isset($this->shh)) {
            $shh = new Shh($this->provider);
            $this->shh = $shh;
        }

        return $this->shh;
    }

    /**
     * 获取工具类实例
     *
     * @return Utils
     */
    public function getUtils(): Utils
    {
        if (!isset($this->utils)) {
            $utils = new Utils();
            $this->utils = $utils;
        }

        return $this->utils;
    }

    /**
     * 批量处理
     *
     * @param bool $status
     */
    public function batch($status): void
    {
        if (!is_bool($status)) {
            throw new InvalidArgumentException('Status must be a boolean value.');
        }

        $this->provider->batch($status);
    }
}
