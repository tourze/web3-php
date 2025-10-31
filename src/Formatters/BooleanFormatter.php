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

class BooleanFormatter implements IFormatter
{
    /**
     * 格式化
     *
     * @param mixed $value
     * @return string
     */
    public static function format($value)
    {
        return (bool) $value ? '1' : '0';
    }
}
