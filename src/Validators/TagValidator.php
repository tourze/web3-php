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

use Tourze\Web3PHP\Utils;

class TagValidator implements IValidator
{
    /**
     * 验证
     *
     * @param mixed $value
     *
     * @return bool
     */
    public static function validate($value)
    {
        $value = Utils::toString($value);
        $tags = [
            'latest', 'earliest', 'pending',
        ];

        return in_array($value, $tags, true);
    }
}
