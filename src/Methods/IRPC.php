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

interface IRPC
{
    /**
     * 字符串转换
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * 转换为载荷
     *
     * @return array<string, mixed>
     */
    public function toPayload(): array;
}
