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

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\StreamInterface;
use RuntimeException as RPCException;
use Tourze\Web3PHP\Exception\InvalidArgumentException;

class HttpRequestManager extends RequestManager implements IRequestManager
{
    /**
     * HTTP 客户端
     *
     * @var Client
     */
    protected $client;

    /**
     * 构造函数
     *
     * @param string $host
     * @param int    $timeout
     */
    public function __construct(string $host, int $timeout = 1)
    {
        parent::__construct($host, $timeout);
        $this->client = new Client();
    }

    /**
     * 发送负载数据
     *
     * @param string   $payload
     * @param callable $callback
     */
    public function sendPayload(string $payload, callable $callback): void
    {
        try {
            $response = $this->makeHttpRequest($payload);
            $json = $this->parseResponse($response);

            if (JSON_ERROR_NONE !== json_last_error()) {
                call_user_func($callback, new InvalidArgumentException('json_decode error: ' . json_last_error_msg()), null);

                return;
            }

            $this->handleResponse($json, $callback);
        } catch (RequestException $err) {
            call_user_func($callback, $err, null);
        }
    }

    /**
     * 发起HTTP请求
     */
    private function makeHttpRequest(string $payload): StreamInterface
    {
        $res = $this->client->post($this->host, [
            'headers' => [
                'content-type' => 'application/json',
            ],
            'body' => $payload,
            'timeout' => $this->timeout,
            'connect_timeout' => $this->timeout,
        ]);

        return $res->getBody();
    }

    /**
     * 解析响应
     */
    private function parseResponse(StreamInterface $stream): mixed
    {
        $json = json_decode($stream->getContents());
        $stream->close();

        return $json;
    }

    /**
     * 处理响应
     * @param mixed $json
     */
    private function handleResponse($json, callable $callback): void
    {
        if (is_array($json)) {
            $this->handleBatchResponse($json, $callback);

            return;
        }

        $this->handleSingleResponse($json, $callback);
    }

    /**
     * 处理批处理响应
     * @param array<mixed> $json
     */
    private function handleBatchResponse(array $json, callable $callback): void
    {
        $results = [];
        $errors = [];

        foreach ($json as $result) {
            $batchResult = $this->processBatchResult($result);
            $results = array_merge($results, $batchResult['results']);
            $errors = array_merge($errors, $batchResult['errors']);
        }

        if (count($errors) > 0) {
            call_user_func($callback, $errors, $results);
        } else {
            call_user_func($callback, null, $results);
        }
    }

    /**
     * 处理批处理结果项
     * @param mixed $result
     * @return array{results: array<mixed>, errors: array<RPCException>}
     */
    private function processBatchResult($result): array
    {
        if (property_exists($result, 'result')) {
            return [
                'results' => [$result->result],
                'errors' => [],
            ];
        }

        if (isset($result->error)) {
            $error = $result->error;

            return [
                'results' => [],
                'errors' => [new RPCException($this->processErrorMessage($error->message), $error->code)],
            ];
        }

        return [
            'results' => [null],
            'errors' => [],
        ];
    }

    /**
     * 处理单个响应
     * @param mixed $json
     */
    private function handleSingleResponse($json, callable $callback): void
    {
        if (isset($json->result)) {
            call_user_func($callback, null, $json->result);

            return;
        }

        if (isset($json->error)) {
            $error = $json->error;
            call_user_func($callback, new RPCException($this->processErrorMessage($error->message), $error->code), null);

            return;
        }

        call_user_func($callback, new RPCException('Something wrong happened.'), null);
    }

    /**
     * 处理错误消息
     */
    private function processErrorMessage(mixed $message): string
    {
        if (!is_string($message)) {
            return 'Unknown error';
        }

        $processedMessage = mb_ereg_replace('Error: ', '', $message);

        return false !== $processedMessage && null !== $processedMessage ? $processedMessage : $message;
    }
}
