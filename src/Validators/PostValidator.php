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

class PostValidator extends BaseValidator
{
    /**
     * 验证
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

        return self::validatePostStructure($value);
    }

    /**
     * 验证Post结构
     * @param array<string, mixed> $value
     */
    private static function validatePostStructure(array $value): bool
    {
        // 验证可选的from和to字段
        if (!self::validateOptional($value, 'from', IdentityValidator::class)) {
            return false;
        }

        if (!self::validateOptional($value, 'to', IdentityValidator::class)) {
            return false;
        }

        // 验证必填字段
        $requiredFields = ['topics', 'payload', 'priority', 'ttl'];
        foreach ($requiredFields as $field) {
            if (!self::validateRequired($value, $field)) {
                return false;
            }
        }

        // 验证topics数组
        if (!self::validateTopics($value['topics'])) {
            return false;
        }

        // 验证其他必填字段的值
        $fieldValidators = [
            'payload' => HexValidator::class,
            'priority' => QuantityValidator::class,
            'ttl' => QuantityValidator::class,
        ];

        foreach ($fieldValidators as $field => $validator) {
            if (!$validator::validate($value[$field])) {
                return false;
            }
        }

        return true;
    }

    /**
     * 验证topics数组
     * @param mixed $topics
     */
    private static function validateTopics($topics): bool
    {
        if (!is_array($topics)) {
            return false;
        }

        return self::validateArrayItems($topics, IdentityValidator::class);
    }
}
