<?php

/**
 * 此文件是 web3.php 包的一部分。
 *
 * (c) Kuan-Cheng,Lai <alk03073135@gmail.com>
 *
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */

namespace Tourze\Web3PHP\Providers;

use Tourze\Web3PHP\Exception\BadMethodCallException;
use Tourze\Web3PHP\RequestManagers\RequestManager;

abstract class Provider implements IProvider
{
    /**
     * requestManager
     *
     * @var RequestManager
     */
    protected $requestManager;

    /**
     * isBatch
     *
     * @var bool
     */
    protected $isBatch = false;

    /**
     * 批量请求
     *
     * @var array<int, mixed>
     */
    protected $batch = [];

    /**
     * rpcVersion
     *
     * @var string
     */
    protected $rpcVersion = '2.0';

    /**
     * id
     *
     * @var int
     */
    protected $id = 0;

    /**
     * 构造函数
     */
    public function __construct(RequestManager $requestManager)
    {
        $this->requestManager = $requestManager;
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
            $callable = [$this, $method];
            if (is_callable($callable)) {
                return call_user_func_array($callable, []);
            }
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
            $callable = [$this, $method];
            if (is_callable($callable)) {
                call_user_func_array($callable, [$value]);
            }
        }
    }

    /**
     * 获取请求管理器
     *
     * @return RequestManager
     */
    public function getRequestManager(): RequestManager
    {
        return $this->requestManager;
    }

    /**
     * 获取是否批量模式
     *
     * @return bool
     */
    public function getIsBatch(): bool
    {
        return $this->isBatch;
    }

    /**
     * isBatch
     *
     * @return bool
     */
    public function isBatch(): bool
    {
        return $this->isBatch;
    }

    /**
     * 设置批量模式
     *
     * @param bool $status
     */
    public function batch(bool $status): void
    {
        // This method should be overridden by subclasses
        throw new BadMethodCallException('This provider does not support batch operations');
    }

    /**
     * 发送请求
     *
     * @param mixed $method
     * @param callable|null $callback
     * @return mixed
     */
    abstract public function send($method, $callback);

    /**
     * 执行请求
     *
     * @param callable|null $callback
     * @return mixed
     */
    public function execute($callback)
    {
        throw new BadMethodCallException('This provider does not support batch execution');
    }
}
