<?php

declare(strict_types=1);

/**
 * 此文件是 web3.php 包的一部分。
 *
 * (c) Kuan-Cheng,Lai <alk03073135@gmail.com>
 *
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */

namespace Tourze\Web3PHP;

use phpseclib3\Math\BigInteger as BigNumber;
use Tourze\Web3PHP\Exception\InvalidArgumentException;

/**
 * 十六进制工具类
 */
class HexUtils
{
    /**
     * 十六进制编码
     * @param mixed $value
     */
    public static function toHex(mixed $value, bool $isPrefix = false): string
    {
        self::validateHexInput($value);
        $hex = self::convertToHex($value);

        return $isPrefix ? '0x' . $hex : $hex;
    }

    /**
     * 验证十六进制输入
     */
    private static function validateHexInput(mixed $value): void
    {
        if (!is_string($value) && !is_int($value) && !($value instanceof BigNumber)) {
            throw new InvalidArgumentException('The value to toHex function is not support.');
        }
    }

    /**
     * 转换为十六进制
     */
    private static function convertToHex(mixed $value): string
    {
        if (is_numeric($value)) {
            return self::convertNumericToHex($value);
        }

        if (is_string($value)) {
            return self::convertStringToHex($value);
        }

        if ($value instanceof BigNumber) {
            return self::convertBigNumberToHex($value);
        }

        throw new InvalidArgumentException('Unsupported value type for hex conversion');
    }

    /**
     * 转换数值为十六进制
     */
    private static function convertNumericToHex(mixed $value): string
    {
        $bn = Utils::toBn($value);
        if (is_array($bn)) {
            throw new InvalidArgumentException('Numeric values cannot be fractional for hex conversion');
        }

        $hex = $bn->toHex(true);

        return self::normalizeHex($hex);
    }

    /**
     * 转换字符串为十六进制
     */
    private static function convertStringToHex(string $value): string
    {
        $cleanValue = self::stripZero($value);
        $packed = unpack('H*', $cleanValue);

        return is_array($packed) ? implode('', $packed) : '';
    }

    /**
     * 转换BigNumber为十六进制
     */
    private static function convertBigNumberToHex(BigNumber $value): string
    {
        $hex = $value->toHex(true);

        return self::normalizeHex($hex);
    }

    /**
     * 规范化十六进制字符串
     */
    private static function normalizeHex(string $hex): string
    {
        if ('' === $hex || '0' === $hex) {
            return '0';
        }

        $result = preg_replace('/^0+(?!$)/', '', $hex);

        return $result ?? $hex;
    }

    /**
     * 十六进制转二进制
     */
    public static function hexToBin(mixed $value): string
    {
        if (!is_string($value)) {
            throw new InvalidArgumentException('The value to hexToBin function must be string.');
        }

        if (self::isZeroPrefixed($value)) {
            $value = substr($value, 2);
        }

        return pack('H*', $value);
    }

    /**
     * 检查是否以零前缀开头
     */
    public static function isZeroPrefixed(mixed $value): bool
    {
        if (!is_string($value)) {
            throw new InvalidArgumentException('The value to isZeroPrefixed function must be string.');
        }

        return str_starts_with($value, '0x') || str_starts_with($value, '0X');
    }

    /**
     * 移除0x前缀
     */
    public static function stripZero(string $value): string
    {
        if (!self::isZeroPrefixed($value)) {
            return $value;
        }

        return substr($value, 2);
    }

    /**
     * 检查是否为十六进制字符串
     */
    public static function isHex(mixed $value): bool
    {
        return is_string($value) && 1 === preg_match('/^(0x)?[a-f0-9]*$/i', $value);
    }
}
