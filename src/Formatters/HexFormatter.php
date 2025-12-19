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

use Tourze\Web3PHP\HexUtils;
use Tourze\Web3PHP\Utils;

class HexFormatter implements IFormatter
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
        $value = mb_strtolower($value);

        if (HexUtils::isZeroPrefixed($value)) {
            return $value;
        }

        return HexUtils::toHex($value, true);
    }
}
