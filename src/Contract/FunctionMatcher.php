<?php

/**
 * 此文件是 web3.php 包的一部分。
 *
 * (c) Kuan-Cheng,Lai <alk03073135@gmail.com>
 *
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */

namespace Tourze\Web3PHP\Contract;

use Tourze\Web3PHP\Contracts\Ethabi;
use Tourze\Web3PHP\Exception\InvalidArgumentException;
use Tourze\Web3PHP\Utils;

/**
 * 函数匹配器 - 专门处理函数查找和匹配
 */
class FunctionMatcher
{
    private Ethabi $ethabi;

    public function __construct(Ethabi $ethabi)
    {
        $this->ethabi = $ethabi;
    }

    /**
     * 查找匹配的函数
     *
     * @param array<string, array<string, mixed>> $functions
     * @param string $methodName
     * @param array<mixed> $params
     * @return array{function: array<string, mixed>, encodedData: string, functionName: string}
     */
    public function findMatchingFunction(array $functions, string $methodName, array $params): array
    {
        $methodFunctions = $this->getMethodFunctions($functions, $methodName);
        $expectedParamsCount = count($params);

        foreach ($methodFunctions as $function) {
            if ($expectedParamsCount !== count($function['inputs'])) {
                continue;
            }

            $result = $this->tryEncodeFunction($function, $params);
            if (null !== $result) {
                return $result;
            }
        }

        throw new InvalidArgumentException('Please make sure you have put all function params and callback.');
    }

    /**
     * 查找匹配的调用函数
     *
     * @param array<string, array<string, mixed>> $functions
     * @param string $method
     * @param array<mixed> $arguments
     * @return array{params: array<mixed>, function: array<string, mixed>, remainingArguments: array<mixed>}
     */
    public function findMatchingCallFunction(array $functions, string $method, array $arguments): array
    {
        $methodFunctions = $this->getMethodFunctions($functions, $method);

        foreach ($methodFunctions as $function) {
            $match = $this->tryMatchFunction($function, $arguments);
            if (null !== $match) {
                return $match;
            }
        }

        throw new InvalidArgumentException('Please make sure you have put all function params and callback.');
    }

    /**
     * 获取指定方法的函数列表
     *
     * @param array<string, array<string, mixed>> $functions
     * @param string $method
     * @return array<array<string, mixed>>
     */
    private function getMethodFunctions(array $functions, string $method): array
    {
        $methodFunctions = array_filter($functions, fn ($func) => $func['name'] === $method);

        if ([] === $methodFunctions) {
            throw new InvalidArgumentException('Please make sure the method exists.');
        }

        return $methodFunctions;
    }

    /**
     * 尝试编码函数
     *
     * @param array<string, mixed> $function
     * @param array<mixed> $params
     * @return array{function: array<string, mixed>, encodedData: string, functionName: string}|null
     */
    private function tryEncodeFunction(array $function, array $params): ?array
    {
        try {
            $encodedData = $this->ethabi->encodeParameters($function, $params);
            $functionName = Utils::jsonMethodToString($function);

            return [
                'function' => $function,
                'encodedData' => $encodedData,
                'functionName' => $functionName,
            ];
        } catch (\InvalidArgumentException $e) {
            return null;
        }
    }

    /**
     * 尝试匹配函数
     *
     * @param array<string, mixed> $function
     * @param array<mixed> $arguments
     * @return array{params: array<mixed>, function: array<string, mixed>, remainingArguments: array<mixed>}|null
     */
    private function tryMatchFunction(array $function, array $arguments): ?array
    {
        $paramsLen = count($function['inputs']);

        if ($paramsLen > count($arguments)) {
            return null;
        }

        $params = array_slice($arguments, 0, $paramsLen);

        try {
            $this->ethabi->encodeParameters($function, $params);
            $remainingArguments = array_slice($arguments, $paramsLen);

            return ['params' => $params, 'function' => $function, 'remainingArguments' => $remainingArguments];
        } catch (\InvalidArgumentException $e) {
            return null;
        }
    }
}
