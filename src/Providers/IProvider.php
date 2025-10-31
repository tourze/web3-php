<?php

/**
 * 此文件是 web3.php 包的一部分。
 *
 * (c) Kuan-Cheng,Lai <alk03073135@gmail.com>
 *
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */

namespace Tourze\Web3PHP\Providers;

interface IProvider
{
    /**
     * 发送请求
     *
     * @param object $method
     * @param callable|null $callback
     * @return mixed
     */
    public function send($method, $callback);

    /**
     * 批量处理
     *
     * @param bool $status
     */
    public function batch(bool $status): void;

    /**
     * 执行
     *
     * @param callable|null $callback
     * @return mixed
     */
    public function execute($callback);

    /**
     * 获取当前是否为批处理模式
     *
     * @return bool
     */
    public function getIsBatch(): bool;
}
