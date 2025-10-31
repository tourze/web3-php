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
use Tourze\Web3PHP\RequestManagers\HttpRequestManager;

/**
 * @method void sendTransaction(array<string, mixed> $transaction, callable $callback) 发送交易
 * @method void call(array<string, mixed> $call, string|null $defaultBlock, callable $callback) 调用合约方法
 * @method void estimateGas(array<string, mixed> $transaction, callable $callback) 估算Gas消耗
 * @method void getBalance(string $address, string|null $defaultBlock, callable $callback) 获取余额
 * @method void getCode(string $address, string|null $defaultBlock, callable $callback) 获取合约代码
 * @method void getTransactionCount(string $address, string|null $defaultBlock, callable $callback) 获取交易次数
 * @method void blockNumber(callable $callback) 获取最新区块号
 * @method void gasPrice(callable $callback) 获取Gas价格
 * @method void accounts(callable $callback) 获取账户列表
 * @method void coinbase(callable $callback) 获取挖矿地址
 * @method void hashrate(callable $callback) 获取算力
 * @method void mining(callable $callback) 是否正在挖矿
 * @method void syncing(callable $callback) 是否正在同步
 * @method void protocolVersion(callable $callback) 获取协议版本
 */
class Eth
{
    /**
     * 提供者
     *
     * @var IProvider
     */
    protected $provider;

    /**
     * 方法集合
     *
     * @var array<string, mixed>
     */
    private $methods = [];

    /**
     * 允许的方法
     *
     * @var array<int, string>
     */
    private $allowedMethods = [
        'eth_protocolVersion', 'eth_syncing', 'eth_coinbase', 'eth_mining', 'eth_hashrate', 'eth_gasPrice', 'eth_accounts', 'eth_blockNumber', 'eth_getBalance', 'eth_getStorageAt', 'eth_getTransactionCount', 'eth_getBlockTransactionCountByHash', 'eth_getBlockTransactionCountByNumber', 'eth_getUncleCountByBlockHash', 'eth_getUncleCountByBlockNumber', 'eth_getUncleByBlockHashAndIndex', 'eth_getUncleByBlockNumberAndIndex', 'eth_getCode', 'eth_sign', 'eth_sendTransaction', 'eth_sendRawTransaction', 'eth_call', 'eth_estimateGas', 'eth_getBlockByHash', 'eth_getBlockByNumber', 'eth_getTransactionByHash', 'eth_getTransactionByBlockHashAndIndex', 'eth_getTransactionByBlockNumberAndIndex', 'eth_getTransactionReceipt', 'eth_compileSolidity', 'eth_compileLLL', 'eth_compileSerpent', 'eth_getWork', 'eth_newFilter', 'eth_newBlockFilter', 'eth_newPendingTransactionFilter', 'eth_uninstallFilter', 'eth_getFilterChanges', 'eth_getFilterLogs', 'eth_getLogs', 'eth_submitWork', 'eth_submitHashrate',
    ];

    /**
     * 构造函数
     *
     * @param string|IProvider $provider
     */
    public function __construct($provider)
    {
        if (is_string($provider) && (false !== filter_var($provider, FILTER_VALIDATE_URL))) {
            // check the uri schema
            if (1 === preg_match('/^https?:\/\//', $provider)) {
                $requestManager = new HttpRequestManager($provider);

                $this->provider = new HttpProvider($requestManager);
            }
        } elseif ($provider instanceof IProvider) {
            $this->provider = $provider;
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
        $this->validateProvider();

        if (!$this->isValidMethodName($name)) {
            return null;
        }

        $method = $this->buildMethodName($name);
        $this->validateMethodAllowed($method);

        $callbackResult = $this->extractCallback($arguments);
        $callback = $callbackResult['callback'];
        $arguments = $callbackResult['arguments'];
        $methodObject = $this->getOrCreateMethodObject($method, $name, $arguments);

        if (!method_exists($methodObject, 'validate')) {
            throw new RuntimeException('Method object must implement validate method.');
        }

        if (!$methodObject->validate($arguments)) {
            return null;
        }

        $this->executeMethod($methodObject, $arguments, $callback);

        return null;
    }

    /**
     * 验证提供者是否已设置
     */
    private function validateProvider(): void
    {
        if (null === $this->provider) {
            throw new RuntimeException('Please set provider first.');
        }
    }

    /**
     * 验证方法名是否有效
     */
    private function isValidMethodName(string $name): bool
    {
        return 1 === preg_match('/^[a-zA-Z0-9]+$/', $name);
    }

    /**
     * 构建完整的方法名
     */
    private function buildMethodName(string $name): string
    {
        $class = explode('\\', get_class());

        return strtolower($class[1]) . '_' . $name;
    }

    /**
     * 验证方法是否被允许
     */
    private function validateMethodAllowed(string $method): void
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
        if (method_exists($this->provider, 'getIsBatch') && $this->provider->getIsBatch()) {
            return ['callback' => null, 'arguments' => $arguments];
        }

        $callback = array_pop($arguments);

        if (true !== is_callable($callback)) {
            throw new InvalidArgumentException('The last param must be callback function.');
        }

        return ['callback' => $callback, 'arguments' => $arguments];
    }

    /**
     * 获取或创建方法对象
     * @param array<int, mixed> $arguments
     */
    private function getOrCreateMethodObject(string $method, string $name, array $arguments): object
    {
        if (array_key_exists($method, $this->methods)) {
            return $this->methods[$method];
        }

        $class = explode('\\', get_class());
        $methodClass = sprintf('\Tourze\Web3PHP\Methods\%s\%s', ucfirst($class[1]), ucfirst($name));
        $methodObject = new $methodClass($method, $arguments);
        $this->methods[$method] = $methodObject;

        return $methodObject;
    }

    /**
     * 执行方法调用
     * @param array<int, mixed> $arguments
     */
    private function executeMethod(object $methodObject, array $arguments, ?callable $callback): void
    {
        if (!method_exists($methodObject, 'transform') || !property_exists($methodObject, 'inputFormatters')) {
            throw new RuntimeException('Method object must implement transform method and have inputFormatters property.');
        }

        if (!property_exists($methodObject, 'arguments')) {
            throw new RuntimeException('Method object must have arguments property.');
        }

        $inputs = $methodObject->transform($arguments, $methodObject->inputFormatters);
        $methodObject->arguments = $inputs;
        $this->provider->send($methodObject, $callback);
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
     * @param IProvider $provider
     */
    public function setProvider($provider): void
    {
        if ($provider instanceof IProvider) {
            $this->provider = $provider;

            return;
        }

        throw new \InvalidArgumentException('Provider must be an instance of IProvider');
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
