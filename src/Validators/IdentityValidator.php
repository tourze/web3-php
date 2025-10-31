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

class IdentityValidator
{
    /**
     * 验证
     * To do: check identity length.
     * Spec: 60 bytes, see https://github.com/ethereum/wiki/wiki/JSON-RPC#shh_newidentity
     * 但返回值是 64 字节。
     *
     * @param mixed $value
     *
     * @return bool
     */
    public static function validate($value)
    {
        if (!is_string($value)) {
            return false;
        }

        return preg_match('/^0x[a-fA-F0-9]*$/', $value) >= 1;
    }
}
