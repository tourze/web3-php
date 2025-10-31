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

use phpseclib3\Math\BigInteger as BigNumber;
use Tourze\Web3PHP\Utils;

class BigNumberFormatter implements IFormatter
{
    /**
     * 格式化
     *
     * @param mixed $value
     * @return BigNumber|array<int, mixed>
     */
    public static function format($value)
    {
        $value = Utils::toString($value);

        return Utils::toBn($value);
    }
}
