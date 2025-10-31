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
use Tourze\Web3PHP\Providers\HttpProvider;
use Tourze\Web3PHP\Providers\IProvider;
use Tourze\Web3PHP\RequestManagers\HttpRequestManager;

/**
 * Web3 个人账户管理 for web3.php package
 *
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */
class Personal extends MethodHandler
{
    /**
     * allowedMethods
     *
     * @var array<int, string>
     */
    private $allowedMethods = [
        'personal_listAccounts', 'personal_newAccount', 'personal_unlockAccount',
        'personal_lockAccount', 'personal_sendTransaction',
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
     * 获取允许的方法列表
     * @return array<int, string>
     */
    protected function getAllowedMethods(): array
    {
        return $this->allowedMethods;
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
     * @return IProvider|null
     */
    public function getProvider(): ?IProvider
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

        if (null !== $this->provider) {
            $this->provider->batch($status);
        }
    }
}
