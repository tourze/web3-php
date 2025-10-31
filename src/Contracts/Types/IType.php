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

interface IType
{
    /**
     * isType
     *
     * @param string $name
     *
     * @return bool
     */
    public function isType($name);

    /**
     * isDynamicType
     *
     * @return bool
     */
    public function isDynamicType();

    /**
     * 输入格式化
     *
     * @param string $name
     * @param mixed $value
     *
     * @return string
     */
    public function inputFormat($value, $name);
}
