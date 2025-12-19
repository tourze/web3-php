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

use Tourze\Web3PHP\AddressUtils;
use Tourze\Web3PHP\HexUtils;

class AddressFormatter implements IFormatter
{
    /**
     * 格式化
     * to do: iban
     *
     * @param mixed $value
     * @return string
     */
    public static function format($value)
    {
        $value = (string) $value;

        if (AddressUtils::isAddress($value)) {
            $value = mb_strtolower($value);

            if (HexUtils::isZeroPrefixed($value)) {
                return $value;
            }

            return '0x' . $value;
        }
        $value = IntegerFormatter::format($value, 40);

        return '0x' . $value;
    }
}
