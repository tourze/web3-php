<?php

/**
 * 此文件是 web3.php 包的一部分。
 *
 * (c) Kuan-Cheng,Lai <alk03073135@gmail.com>
 *
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */

namespace Tourze\Web3PHP\RequestManagers;

interface IRequestManager
{
    /**
     * 发送负载数据
     *
     * @param string   $payload
     * @param callable $callback
     */
    public function sendPayload(string $payload, callable $callback): void;
}
