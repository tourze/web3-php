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

use Tourze\Web3PHP\Exception\InvalidArgumentException;

/**
 * 参数处理器 - 专门处理各种参数解析和整理
 */
class ArgumentProcessor
{
    private ArgumentValidator $validator;

    public function __construct(ArgumentValidator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * 处理函数调用参数
     *
     * @param array<mixed> $arguments
     * @return array{method: string, params: array<mixed>, callback: callable, hasTransaction: bool, transaction: array<string, mixed>}
     */
    public function processFunctionArguments(array $arguments): array
    {
        $method = array_shift($arguments);
        $callback = array_pop($arguments);

        $this->validator->validateMethod($method);
        $this->validator->validateCallback($callback);

        $transactionInfo = $this->extractTransactionInfo($arguments);

        return [
            'method' => $method,
            'params' => $transactionInfo['remainingArguments'],
            'callback' => $callback,
            'hasTransaction' => $transactionInfo['hasTransaction'],
            'transaction' => $transactionInfo['transaction'],
        ];
    }

    /**
     * 处理call方法的参数
     *
     * @param array<mixed> $arguments
     * @param mixed $defaultBlock
     * @return array{method: string, callback: callable, transaction: array<string, mixed>, defaultBlock: mixed}
     */
    public function processCallArguments(array $arguments, $defaultBlock): array
    {
        $method = array_shift($arguments);
        $callback = array_pop($arguments);

        $this->validator->validateMethod($method);
        $this->validator->validateCallback($callback);

        $optionalArgs = $this->parseOptionalCallArguments($arguments, $defaultBlock);

        return [
            'method' => $method,
            'callback' => $callback,
            'transaction' => $optionalArgs['transaction'],
            'defaultBlock' => $optionalArgs['defaultBlock'],
        ];
    }

    /**
     * 处理构造函数Gas估算参数
     *
     * @param array<mixed> $arguments
     * @param array<string, mixed> $constructor
     * @param string $bytecode
     * @return array{transaction: array<string, mixed>, callback: callable}
     */
    public function processConstructorGasArguments(array $arguments, array $constructor, string $bytecode): array
    {
        $callback = array_pop($arguments);
        $this->validator->validateCallback($callback);

        if ('' === $bytecode) {
            throw new InvalidArgumentException('Please call bytecode($bytecode) before estimateGas().');
        }

        $inputCount = count($constructor['inputs']);
        $this->validator->validateConstructorArguments($arguments, $constructor, $inputCount);

        $transaction = [];
        if (count($arguments) > $inputCount) {
            $transaction = $arguments[$inputCount];
        }

        return [
            'transaction' => $transaction,
            'callback' => $callback,
        ];
    }

    /**
     * 提取事务信息
     *
     * @param array<mixed> $arguments
     * @return array{hasTransaction: bool, transaction: array<string, mixed>, remainingArguments: array<mixed>}
     */
    private function extractTransactionInfo(array $arguments): array
    {
        $argsLen = count($arguments);

        if ($argsLen <= 0) {
            return ['hasTransaction' => false, 'transaction' => [], 'remainingArguments' => []];
        }

        $lastArg = $arguments[$argsLen - 1];
        if (is_array($lastArg) && $this->validator->isTransactionObject($lastArg)) {
            $remainingArguments = array_slice($arguments, 0, -1);

            return ['hasTransaction' => true, 'transaction' => $lastArg, 'remainingArguments' => $remainingArguments];
        }

        return ['hasTransaction' => false, 'transaction' => [], 'remainingArguments' => $arguments];
    }

    /**
     * 解析可选调用参数
     *
     * @param array<mixed> $arguments
     * @param mixed $defaultBlock
     * @return array{transaction: array<string, mixed>, defaultBlock: mixed}
     */
    private function parseOptionalCallArguments(array $arguments, $defaultBlock): array
    {
        $argsLen = count($arguments);

        if ($argsLen > 1) {
            return [
                'transaction' => $this->validator->validateTransaction($arguments[$argsLen - 2]),
                'defaultBlock' => $this->validator->validateDefaultBlock($arguments[$argsLen - 1]),
            ];
        }

        if (1 === $argsLen) {
            $arg = $arguments[0];
            if (is_array($arg) && $this->validator->isTransactionObject($arg)) {
                return ['transaction' => $arg, 'defaultBlock' => $defaultBlock];
            }

            return ['transaction' => [], 'defaultBlock' => $this->validator->validateDefaultBlock($arg)];
        }

        return ['transaction' => [], 'defaultBlock' => $defaultBlock];
    }
}
