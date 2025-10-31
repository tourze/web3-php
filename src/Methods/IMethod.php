<?php

/**
 * 此文件是 web3.php 包的一部分。
 *
 * (c) Kuan-Cheng,Lai <alk03073135@gmail.com>
 *
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */

namespace Tourze\Web3PHP\Methods;

interface IMethod
{
    /**
     * 转换
     *
     * @param array<string, mixed> $data
     * @param array<string, mixed> $rules
     *
     * @return array<string, mixed>
     */
    public function transform($data, $rules);
}
