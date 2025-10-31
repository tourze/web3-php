<?php

/**
 * 此文件是 web3.php 包的一部分。
 *
 * (c) Kuan-Cheng,Lai <alk03073135@gmail.com>
 *
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */

namespace Tourze\Web3PHP\RequestManagers;

use Tourze\Web3PHP\Exception\BadMethodCallException;

abstract class RequestManager
{
    /**
     * 主机地址
     *
     * @var string
     */
    protected $host;

    /**
     * 超时时间
     *
     * @var float
     */
    protected $timeout;

    /**
     * 构造函数
     *
     * @param string $host
     * @param float  $timeout
     */
    public function __construct(string $host, float $timeout = 1)
    {
        $this->host = $host;
        $this->timeout = $timeout;
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
    public function __set(string $name, mixed $value): void
    {
        $method = 'set' . ucfirst($name);

        if (method_exists($this, $method)) {
            /** @var callable $callable */
            $callable = [$this, $method];
            call_user_func_array($callable, [$value]);
        }
    }

    /**
     * 获取主机地址
     *
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * 获取超时时间
     *
     * @return float
     */
    public function getTimeout(): float
    {
        return $this->timeout;
    }

    /**
     * 发送负载数据
     *
     * @param string $payload
     * @param callable $callback
     */
    public function sendPayload(string $payload, callable $callback): void
    {
        // 这是一个抽象方法，需要由子类实现
        throw new BadMethodCallException('This method must be implemented by subclasses');
    }
}
