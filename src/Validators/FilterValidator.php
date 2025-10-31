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

class FilterValidator
{
    /**
     * 验证
     *
     * @param mixed $value
     *
     * @return bool
     */
    public static function validate($value)
    {
        if (!is_array($value)) {
            return false;
        }

        return self::validateBlockRange($value)
            && self::validateAddresses($value)
            && self::validateTopics($value);
    }

    /**
     * 验证区块范围
     * @param array<string, mixed> $value
     */
    private static function validateBlockRange(array $value): bool
    {
        return self::validateBlock($value, 'fromBlock')
            && self::validateBlock($value, 'toBlock');
    }

    /**
     * 验证单个区块
     * @param array<string, mixed> $value
     */
    private static function validateBlock(array $value, string $key): bool
    {
        if (!isset($value[$key])) {
            return true;
        }

        return QuantityValidator::validate($value[$key])
            || TagValidator::validate($value[$key]);
    }

    /**
     * 验证地址
     * @param array<string, mixed> $value
     */
    private static function validateAddresses(array $value): bool
    {
        if (!isset($value['address'])) {
            return true;
        }

        $addresses = is_array($value['address']) ? $value['address'] : [$value['address']];

        foreach ($addresses as $address) {
            if (!AddressValidator::validate($address)) {
                return false;
            }
        }

        return true;
    }

    /**
     * 验证主题
     * @param array<string, mixed> $value
     */
    private static function validateTopics(array $value): bool
    {
        if (!isset($value['topics']) || !is_array($value['topics'])) {
            return true;
        }

        foreach ($value['topics'] as $topic) {
            if (!self::validateSingleTopic($topic)) {
                return false;
            }
        }

        return true;
    }

    /**
     * 验证单个主题
     * @param mixed $topic
     */
    private static function validateSingleTopic($topic): bool
    {
        if (is_array($topic)) {
            return self::validateTopicArray($topic);
        }

        return !isset($topic) || HexValidator::validate($topic);
    }

    /**
     * 验证主题数组
     * @param array<mixed> $topics
     */
    private static function validateTopicArray(array $topics): bool
    {
        foreach ($topics as $v) {
            if (isset($v) && !HexValidator::validate($v)) {
                return false;
            }
        }

        return true;
    }
}
