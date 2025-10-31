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
use Tourze\Web3PHP\Exception\InvalidArgumentException;

class Boolean extends SolidityType implements IType
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
        return 1 === preg_match('/^bool(\[([0-9]*)\])*$/', $name);
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
        if (!is_bool($value)) {
            throw new InvalidArgumentException('输入格式化函数的值必须是布尔型。');
        }
        $value = (int) $value;

        return '000000000000000000000000000000000000000000000000000000000000000' . $value;
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
        $value = (int) mb_substr($value, 63, 1);

        return (bool) $value ? 'true' : 'false';
    }
}
