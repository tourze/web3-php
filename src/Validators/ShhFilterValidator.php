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

class ShhFilterValidator extends BaseValidator
{
    /**
     * 验证
     */
    public static function validate(mixed $value): bool
    {
        if (!self::validateIsArray($value)) {
            return false;
        }

        return self::validateFilterStructure($value);
    }

    /**
     * 验证过滤器结构
     * @param array<string, mixed> $value
     */
    private static function validateFilterStructure(array $value): bool
    {
        // 验证可选的to字段
        if (!self::validateOptional($value, 'to', IdentityValidator::class)) {
            return false;
        }

        // topics字段必填且必须是数组
        if (!self::validateRequired($value, 'topics') || !is_array($value['topics'])) {
            return false;
        }

        return self::validateTopics($value['topics']);
    }

    /**
     * 验证topics数组
     * @param array<mixed> $topics
     */
    private static function validateTopics(array $topics): bool
    {
        foreach ($topics as $topic) {
            if (!self::validateTopic($topic)) {
                return false;
            }
        }

        return true;
    }

    /**
     * 验证单个topic
     */
    private static function validateTopic(mixed $topic): bool
    {
        if (is_null($topic)) {
            return true;
        }

        if (is_array($topic)) {
            return self::validateArrayItems($topic, HexValidator::class);
        }

        return HexValidator::validate($topic);
    }
}
