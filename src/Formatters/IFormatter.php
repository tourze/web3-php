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

interface IFormatter
{
    /**
     * 格式化
     *
     * @param mixed $value
     * @return mixed
     */
    public static function format($value);
}
