<?php

/**
 * 此文件是 web3.php 包的一部分。
 *
 * (c) Kuan-Cheng,Lai <alk03073135@gmail.com>
 *
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */

namespace Tourze\Web3PHP\Contracts\Types;

use Tourze\Web3PHP\Contracts\SolidityType;
use Tourze\Web3PHP\Formatters\BigNumberFormatter;
use Tourze\Web3PHP\Formatters\IntegerFormatter;
use Tourze\Web3PHP\Utils;

class Str extends SolidityType implements IType
{
    /**
     * 构造函数
     */
    public function __construct()
    {
    }

    /**
     * isType
     *
     * @param string $name
     *
     * @return bool
     */
    public function isType($name)
    {
        return 1 === preg_match('/^string(\[([0-9]*)\])*$/', $name);
    }

    /**
     * isDynamicType
     *
     * @return bool
     */
    public function isDynamicType()
    {
        return true;
    }

    /**
     * 输入格式化
     *
     * @param mixed $value
     * @param string $name
     *
     * @return string
     */
    public function inputFormat($value, $name): string
    {
        $value = Utils::toHex($value);
        $prefix = IntegerFormatter::format(mb_strlen($value) / 2);
        $l = floor((mb_strlen($value) + 63) / 64);
        $padding = (($l * 64 - mb_strlen($value) + 1) >= 0) ? $l * 64 - mb_strlen($value) : 0;

        return '0x' . $prefix . $value . implode('', array_fill(0, (int) $padding, '0'));
    }

    /**
     * 输出格式化
     *
     * @param mixed $value
     * @param string $name
     *
     * @return string
     */
    public function outputFormat($value, $name): string
    {
        $strLen = mb_substr($value, 0, 64);
        $strValue = mb_substr($value, 64);
        $match = [];

        if (1 === preg_match('/^[0]+([a-f0-9]+)$/', $strLen, $match)) {
            $formatted = BigNumberFormatter::format('0x' . $match[1]);
            $strLen = is_array($formatted) ? (string) $formatted[0] : $formatted->toString();
        }
        $strValue = mb_substr($strValue, 0, (int) $strLen * 2);

        return Utils::hexToBin($strValue);
    }
}
