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

use Tourze\Web3PHP\Formatters\AddressFormatter;
use Tourze\Web3PHP\Formatters\OptionalQuantityFormatter;
use Tourze\Web3PHP\Methods\EthMethod;
use Tourze\Web3PHP\Validators\AddressValidator;
use Tourze\Web3PHP\Validators\QuantityValidator;
use Tourze\Web3PHP\Validators\TagValidator;

class GetCode extends EthMethod
{
    /**
     * 验证器
     *
     * @var array<int, mixed>
     */
    protected $validators = [
        AddressValidator::class, [
            TagValidator::class, QuantityValidator::class,
        ],
    ];

    /**
     * 输入格式化器
     *
     * @var array<int, mixed>
     */
    protected $inputFormatters = [
        AddressFormatter::class, OptionalQuantityFormatter::class,
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
    protected $defaultValues = [
        1 => 'latest',
    ];

    /*
     * 构造函数
     *
     * @param string $method
     * @param array $arguments
     * @return void
     */
    public function __construct($method = 'eth__get_code', $arguments = [])
    {
        parent::__construct($method, $arguments);
    }
}
