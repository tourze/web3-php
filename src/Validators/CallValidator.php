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

class CallValidator extends BaseValidator
{
    /**
     * 验证
     */
    public static function validate(mixed $value): bool
    {
        if (!self::validateIsArray($value)) {
            return false;
        }

        return self::validateCallStructure($value);
    }

    /**
     * 验证调用结构
     * @param array<string, mixed> $value
     */
    private static function validateCallStructure(array $value): bool
    {
        // to 字段必填
        if (!self::validateRequired($value, 'to') || !AddressValidator::validate($value['to'])) {
            return false;
        }

        // 可选字段验证
        $optionalFields = [
            'from' => AddressValidator::class,
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
}
