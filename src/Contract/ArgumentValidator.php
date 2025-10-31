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
use Tourze\Web3PHP\Validators\QuantityValidator;
use Tourze\Web3PHP\Validators\TagValidator;

/**
 * 参数验证器 - 专门处理各种参数验证
 */
class ArgumentValidator
{
    /**
     * 验证方法名
     *
     * @param mixed $method
     * @throws InvalidArgumentException
     */
    public function validateMethod($method): void
    {
        if (!is_string($method)) {
            throw new InvalidArgumentException('Please make sure the method is string.');
        }
    }

    /**
     * 验证回调函数
     *
     * @param mixed $callback
     * @throws InvalidArgumentException
     */
    public function validateCallback($callback): void
    {
        if (!is_callable($callback)) {
            throw new InvalidArgumentException('The last param must be callback function.');
        }
    }

    /**
     * 验证默认区块
     *
     * @param mixed $defaultBlock
     * @return mixed
     */
    public function validateDefaultBlock($defaultBlock)
    {
        return (TagValidator::validate($defaultBlock) || QuantityValidator::validate($defaultBlock))
            ? $defaultBlock
            : 'latest';
    }

    /**
     * 验证交易对象
     *
     * @param mixed $transaction
     * @return array<string, mixed>
     */
    public function validateTransaction($transaction): array
    {
        return (is_array($transaction) && $this->isTransactionObject($transaction)) ? $transaction : [];
    }

    /**
     * 验证构造函数参数
     *
     * @param array<mixed> $arguments
     * @param array<string, mixed> $constructor
     * @param int $expectedCount
     * @throws InvalidArgumentException
     */
    public function validateConstructorArguments(array $arguments, array $constructor, int $expectedCount): void
    {
        if (count($arguments) < $expectedCount) {
            throw new InvalidArgumentException('Please make sure you have put all constructor params and callback.');
        }
    }

    /**
     * 检测是否为事务对象
     *
     * @param array<string, mixed> $data
     * @return bool
     */
    public function isTransactionObject(array $data): bool
    {
        $transactionFields = ['from', 'to', 'gas', 'gasPrice', 'value', 'data', 'nonce'];

        foreach ($transactionFields as $field) {
            if (isset($data[$field])) {
                return true;
            }
        }

        return false;
    }
}
