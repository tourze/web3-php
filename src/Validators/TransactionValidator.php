<?php

/**
 * 此文件是 web3.php 包的一部分。
 *
 * (c) Kuan-Cheng,Lai <alk03073135@gmail.com>
 *
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */

namespace Tourze\Web3PHP\Validators;

class TransactionValidator extends BaseValidator
{
    /**
     * 验证
     * To do: check is data optional?
     * Data is not optional on spec, see https://github.com/ethereum/wiki/wiki/JSON-RPC#eth_sendtransaction
     *
     * @param mixed $value
     *
     * @return bool
     */
    public static function validate($value): bool
    {
        if (!self::validateIsArray($value)) {
            return false;
        }

        return self::validateTransactionStructure($value);
    }

    /**
     * 验证交易结构
     * @param array<string, mixed> $value
     */
    private static function validateTransactionStructure(array $value): bool
    {
        // from字段必填
        if (!self::validateRequired($value, 'from') || !AddressValidator::validate($value['from'])) {
            return false;
        }

        // to字段特殊验证（可以为空字符串）
        if (!self::validateToField($value)) {
            return false;
        }

        // 验证可选字段
        $optionalFields = [
            'gas' => QuantityValidator::class,
            'gasPrice' => QuantityValidator::class,
            'value' => QuantityValidator::class,
            'data' => HexValidator::class,
            'nonce' => QuantityValidator::class,
        ];

        foreach ($optionalFields as $field => $validator) {
            if (!self::validateOptional($value, $field, $validator)) {
                return false;
            }
        }

        return true;
    }

    /**
     * 验证to字段（特殊逻辑：可以为空字符串）
     * @param array<string, mixed> $value
     */
    private static function validateToField(array $value): bool
    {
        if (!isset($value['to'])) {
            return true;
        }

        return AddressValidator::validate($value['to']) || '' === $value['to'];
    }
}
