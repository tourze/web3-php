<?php

/**
 * 此文件是 web3.php 包的一部分。
 *
 * (c) Kuan-Cheng,Lai <alk03073135@gmail.com>
 *
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */

namespace Tourze\Web3PHP\Validators;

class QuantityValidator
{
    /**
     * 验证
     *
     * @param mixed $value
     *
     * @return bool
     */
    public static function validate($value): bool
    {
        // maybe change is_int and is_float and preg_match future
        return is_int($value) || is_float($value) || (is_string($value) && preg_match('/^0x[a-fA-F0-9]*$/', $value) >= 1);
    }
}
