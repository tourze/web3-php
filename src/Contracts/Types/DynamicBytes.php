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
use Tourze\Web3PHP\Utils;

class DynamicBytes extends SolidityType implements IType
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
        return 1 === preg_match('/^bytes(\[([0-9]*)\])*$/', $name);
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
        if (!Utils::isHex($value)) {
            throw new InvalidArgumentException('输入格式化的值必须是十六进制字节。');
        }
        $value = Utils::stripZero($value);

        if (0 !== mb_strlen($value) % 2) {
            $value = '0' . $value;
            // throw new InvalidArgumentException('输入格式化的值长度无效。');
        }
        $bn = Utils::toBn((int) floor(mb_strlen($value) / 2));
        if (is_array($bn)) {
            throw new InvalidArgumentException('期望整数用于长度计算，但得到分数。');
        }
        $bnHex = $bn->toHex(true);
        $padded = mb_substr($bnHex, 0, 1);

        if ('0' !== $padded && 'f' !== $padded) {
            $padded = '0';
        }
        $l = floor((mb_strlen($value) + 63) / 64);
        $padding = (($l * 64 - mb_strlen($value) + 1) >= 0) ? $l * 64 - mb_strlen($value) : 0;

        return implode('', array_fill(0, 64 - mb_strlen($bnHex), $padded)) . $bnHex . $value . implode('', array_fill(0, (int) $padding, '0'));
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
        $checkZero = str_replace('0', '', $value);

        if ('' === $checkZero) {
            return '0';
        }
        $sizeBn = Utils::toBn('0x' . mb_substr($value, 0, 64));
        if (is_array($sizeBn)) {
            throw new InvalidArgumentException('期望整数用于大小计算，但得到分数。');
        }
        $size = intval($sizeBn->toString());
        $length = 2 * $size;

        return '0x' . mb_substr($value, 64, $length);
    }
}
