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

class NumberFormatter implements IFormatter
{
    /**
     * 格式化
     *
     * @param mixed $value
     * @return int
     */
    public static function format($value)
    {
        $value = Utils::toString($value);
        $bn = Utils::toBn($value);
        if (is_array($bn)) {
            throw new InvalidArgumentException('NumberFormatter期望整数值，但得到分数。');
        }

        return (int) $bn->toString();
    }
}
