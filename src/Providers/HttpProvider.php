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

use Tourze\Web3PHP\Exception\InvalidArgumentException;
use Tourze\Web3PHP\Exception\RuntimeException;
use Tourze\Web3PHP\RequestManagers\RequestManager;

class HttpProvider extends Provider implements IProvider
{
    /**
     * 方法列表
     *
     * @var array<int, object>
     */
    protected $methods = [];

    /**
     * 构造函数
     */
    public function __construct(RequestManager $requestManager)
    {
        parent::__construct($requestManager);
    }

    /**
     * 发送请求
     *
     * @param mixed $method
     * @param callable|null $callback
     * @return mixed
     */
    public function send($method, $callback)
    {
        $payload = $method->toPayloadString();

        if (!$this->isBatch) {
            $proxy = $this->createSingleRequestProxy($method, $callback);
            $this->requestManager->sendPayload($payload, $proxy);

            return null;
        }
        $this->methods[] = $method;
        $this->batch[] = $payload;

        return null;
    }

    /**
     * 批量处理
     *
     * @param bool $status
     */
    public function batch(bool $status): void
    {
        $this->isBatch = $status;
    }

    /**
     * 执行批量请求
     *
     * @param callable|null $callback
     * @return mixed
     */
    public function execute($callback)
    {
        $this->validateBatchState();

        $methods = $this->methods;
        $proxy = $this->createBatchProxy($methods, $callback);

        $this->sendBatchRequest($proxy);
        $this->resetBatch();

        return null;
    }

    /**
     * 验证批处理状态
     */
    private function validateBatchState(): void
    {
        if (!$this->isBatch) {
            throw new RuntimeException('Please batch json rpc first.');
        }
    }

    /**
     * 创建批处理代理
     * @param array<int, object> $methods
     * @param callable|null $callback
     */
    private function createBatchProxy(array $methods, ?callable $callback): callable
    {
        return function ($err, $res) use ($methods, $callback) {
            if (null !== $err) {
                if (null !== $callback) {
                    return call_user_func($callback, $err, null);
                }

                return null;
            }

            $res = $this->processMethodResults($methods, $res);

            if (null !== $callback) {
                return call_user_func($callback, null, $res);
            }

            return null;
        };
    }

    /**
     * 处理方法结果
     * @param array<int, object> $methods
     */
    private function processMethodResults(array $methods, mixed $res): mixed
    {
        foreach ($methods as $key => $method) {
            if (isset($res[$key])) {
                $res[$key] = $this->formatMethodResult($method, $res[$key]);
            }
        }

        return $res;
    }

    /**
     * 格式化方法结果
     */
    private function formatMethodResult(object $method, mixed $result): mixed
    {
        if (!is_array($result)) {
            if (!method_exists($method, 'formatOutput')) {
                throw new RuntimeException('Method object must implement formatOutput method.');
            }

            return $method->formatOutput($result);
        }

        if (!method_exists($method, 'transform') || !property_exists($method, 'outputFormatters')) {
            throw new RuntimeException('Method object must implement transform method and have outputFormatters property.');
        }

        return $method->transform($result, $method->outputFormatters);
    }

    /**
     * 发送批处理请求
     */
    private function sendBatchRequest(callable $proxy): void
    {
        $payload = '[' . implode(',', $this->batch) . ']';
        $this->requestManager->sendPayload($payload, $proxy);
    }

    /**
     * 重置批处理
     */
    private function resetBatch(): void
    {
        $this->methods = [];
        $this->batch = [];
    }

    /**
     * 创建单个请求代理 - 简化复杂度
     * @param mixed $method
     * @param mixed $callback
     */
    private function createSingleRequestProxy($method, $callback): callable
    {
        return function ($err, $res) use ($method, $callback) {
            if (null !== $err) {
                if (null !== $callback) {
                    return call_user_func($callback, $err, null);
                }

                return null;
            }

            $formattedResult = $this->formatSingleResult($method, $res);

            if (null !== $callback) {
                return call_user_func($callback, null, $formattedResult);
            }

            return null;
        };
    }

    /**
     * 格式化单个结果
     * @param mixed $method
     * @param mixed $res
     */
    private function formatSingleResult($method, $res): mixed
    {
        if (!is_array($res)) {
            if (!is_object($method) || !method_exists($method, 'formatOutput')) {
                throw new RuntimeException('Method object must implement formatOutput method.');
            }

            return $method->formatOutput($res);
        }

        if (!is_object($method) || !method_exists($method, 'transform') || !property_exists($method, 'outputFormatters')) {
            throw new RuntimeException('Method object must implement transform method and have outputFormatters property.');
        }

        return $method->transform($res, $method->outputFormatters);
    }
}
