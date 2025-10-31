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
use Tourze\Web3PHP\Utils;

class IntegerFormatter implements IFormatter
{
    /**
     * 格式化
     *
     * @param mixed $value
     * @return string
     */
    public static function format($value)
    {
        $value = (string) $value;
        $arguments = func_get_args();
        $digit = 64;

        if (isset($arguments[1]) && is_numeric($arguments[1])) {
            $digit = intval($arguments[1]);
        }
        $bn = Utils::toBn($value);
        if (is_array($bn)) {
            throw new InvalidArgumentException('IntegerFormatter期望整数值，但得到分数。');
        }
        $bnHex = $bn->toHex(true);
        $padded = mb_substr($bnHex, 0, 1);

        if ('f' !== $padded) {
            $padded = '0';
        }

        return implode('', array_fill(0, $digit - mb_strlen($bnHex), $padded)) . $bnHex;
    }
}
