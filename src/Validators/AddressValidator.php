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

class AddressValidator
{
    /**
     * 验证
     *
     * @param string $value
     *
     * @return bool
     */
    public static function validate($value)
    {
        if (!is_string($value)) {
            return false;
        }

        return preg_match('/^0x[a-fA-F0-9]{40}$/', $value) >= 1;
    }
}
