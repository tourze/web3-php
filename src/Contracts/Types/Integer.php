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

class Integer extends SolidityType implements IType
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
        return 1 === preg_match('/^int([0-9]{1,})?(\[([0-9]*)\])*$/', $name);
    }

    /**
     * isDynamicType
     *
     * @return bool
     */
    public function isDynamicType()
    {
        return false;
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
        return '0x' . IntegerFormatter::format($value);
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
        $match = [];

        if (1 === preg_match('/^[0]+([a-f0-9]+)$/', $value, $match)) {
            // due to value without 0x prefix, we will parse as decimal
            $value = '0x' . $match[1];
        }

        $formatted = BigNumberFormatter::format($value);

        return is_array($formatted) ? (string) $formatted[0] : $formatted->toString();
    }
}
