<?php

/**
 * 此文件是 web3.php 包的一部分。
 *
 * (c) Kuan-Cheng,Lai <alk03073135@gmail.com>
 *
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */

namespace Tourze\Web3PHP\Methods;

use Tourze\Web3PHP\Exception\InvalidArgumentException;

class JsonRpc
{
    /**
     * id
     *
     * @var int
     */
    protected $id = 0;

    /**
     * JSON-RPC 版本
     *
     * @var string
     */
    protected $jsonrpc = '2.0';

    /**
     * 方法
     *
     * @var string
     */
    protected $method = '';

    /**
     * 参数
     *
     * @var array<int|string, mixed>
     */
    protected $params = [];

    /**
     * 构造函数
     *
     * @param string $method
     * @param array<int|string, mixed>  $params
     * @param int    $id
     */
    public function __construct(string $method = '', array $params = [], int $id = 1)
    {
        $this->method = $method;
        $this->params = $params;
        $this->id = $id;
    }

    /**
     * 获取ID
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * 设置ID
     *
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * 获取JSON RPC版本
     *
     * @return string
     */
    public function getJsonRpc(): string
    {
        return $this->jsonrpc;
    }

    /**
     * 设置JSON RPC版本
     *
     * @param string $jsonrpc
     */
    public function setJsonRpc(string $jsonrpc): void
    {
        $this->jsonrpc = $jsonrpc;
    }

    /**
     * 获取方法名
     *
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * 设置方法名
     *
     * @param string $method
     */
    public function setMethod(string $method): void
    {
        $this->method = $method;
    }

    /**
     * 获取参数
     *
     * @return array<int|string, mixed>
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * 设置参数
     *
     * @param array<int|string, mixed> $params
     */
    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    /**
     * toArray
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'jsonrpc' => $this->jsonrpc,
            'method' => $this->method,
            'params' => $this->params,
        ];
    }

    /**
     * 字符串转换
     *
     * @return string
     */
    public function __toString(): string
    {
        $result = json_encode($this->toArray());

        return false === $result ? '' : $result;
    }
}
