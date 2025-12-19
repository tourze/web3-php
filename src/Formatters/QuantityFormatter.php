<?php

/**
 * 此文件是 web3.php 包的一部分。
 *
 * (c) Kuan-Cheng,Lai <alk03073135@gmail.com>
 *
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */

namespace Tourze\Web3PHP\Formatters;

use Tourze\Web3PHP\Exception\InvalidArgumentException;
use Tourze\Web3PHP\HexUtils;
use Tourze\Web3PHP\Utils;

class QuantityFormatter implements IFormatter
{
    /**
     * 格式化
     *
     * @param mixed $value
     * @return string
     */
    public static function format($value)
    {
        $value = Utils::toString($value);
        $hex = self::processValue($value);
        $hex = self::normalizeHex($hex);

        return '0x' . $hex;
    }

    /**
     * 处理值并转换为十六进制
     */
    private static function processValue(string $value): string
    {
        if (HexUtils::isZeroPrefixed($value)) {
            return self::processHexValue($value);
        }

        return self::processDecimalValue($value);
    }

    /**
     * 处理十六进制值
     */
    private static function processHexValue(string $value): string
    {
        // test hex with zero ahead, hardcode 0x0
        if ('0x0' === $value || 0 !== strpos($value, '0x0')) {
            return ltrim($value, '0x');
        }

        $hex = preg_replace('/^0x0+(?!$)/', '', $value);
        if (null === $hex) {
            throw new InvalidArgumentException('正则表达式替换失败。');
        }

        return $hex;
    }

    /**
     * 处理十进制值
     */
    private static function processDecimalValue(string $value): string
    {
        $bn = Utils::toBn($value);
        if (is_array($bn)) {
            throw new InvalidArgumentException('QuantityFormatter期望整数值，但得到分数。');
        }

        return $bn->toHex(true);
    }

    /**
     * 标准化十六进制字符串
     */
    private static function normalizeHex(string $hex): string
    {
        if ('' === $hex) {
            return '0';
        }

        $processedHex = preg_replace('/^0+(?!$)/', '', $hex);
        if (null === $processedHex) {
            throw new InvalidArgumentException('正则表达式替换失败。');
        }

        return $processedHex;
    }
}
