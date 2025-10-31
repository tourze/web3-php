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

abstract class BaseValidator
{
    /**
     * 验证数组类型
     */
    protected static function validateIsArray(mixed $value): bool
    {
        return is_array($value);
    }

    /**
     * 验证必填字段
     * @param array<string, mixed> $value
     */
    protected static function validateRequired(array $value, string $field): bool
    {
        return isset($value[$field]);
    }

    /**
     * 验证可选字段
     * @param array<string, mixed> $value
     */
    protected static function validateOptional(array $value, string $field, string $validator): bool
    {
        if (!isset($value[$field])) {
            return true;
        }

        return $validator::validate($value[$field]);
    }

    /**
     * 验证字段值
     * @param array<string, mixed> $value
     */
    protected static function validateField(array $value, string $field, string $validator): bool
    {
        if (!isset($value[$field])) {
            return false;
        }

        return $validator::validate($value[$field]);
    }

    /**
     * 验证数组中的每个项目
     * @param array<mixed> $items
     */
    protected static function validateArrayItems(array $items, string $validator): bool
    {
        foreach ($items as $item) {
            if (!$validator::validate($item)) {
                return false;
            }
        }

        return true;
    }
}
