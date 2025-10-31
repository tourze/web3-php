<?php

/**
 * 此文件是 web3.php 包的一部分。
 *
 * (c) Kuan-Cheng,Lai <alk03073135@gmail.com>
 *
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */

namespace Tourze\Web3PHP\Methods\Eth;

use Tourze\Web3PHP\Formatters\HexFormatter;
use Tourze\Web3PHP\Methods\EthMethod;
use Tourze\Web3PHP\Validators\BlockHashValidator;

class GetTransactionByHash extends EthMethod
{
    /**
     * 验证器
     *
     * @var array<int, mixed>
     */
    protected $validators = [
        BlockHashValidator::class,
    ];

    /**
     * 输入格式化器
     *
     * @var array<int, mixed>
     */
    protected $inputFormatters = [
        HexFormatter::class,
    ];

    /**
     * 输出格式化器
     *
     * @var array<int, mixed>
     */
    protected $outputFormatters = [];

    /**
     * 默认值
     *
     * @var array<int, mixed>
     */
    protected $defaultValues = [];

    /*
     * 构造函数
     *
     * @param string $method
     * @param array $arguments
     * @return void
     */
    public function __construct($method = 'eth__get_transaction_by_hash', $arguments = [])
    {
        parent::__construct($method, $arguments);
    }
}
