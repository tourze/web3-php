<?php

/**
 * 此文件是 web3.php 包的一部分。
 *
 * (c) Kuan-Cheng,Lai <alk03073135@gmail.com>
 *
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */

namespace Tourze\Web3PHP\Methods\Personal;

use Tourze\Web3PHP\Formatters\AddressFormatter;
use Tourze\Web3PHP\Formatters\NumberFormatter;
use Tourze\Web3PHP\Formatters\StringFormatter;
use Tourze\Web3PHP\Methods\EthMethod;
use Tourze\Web3PHP\Validators\AddressValidator;
use Tourze\Web3PHP\Validators\QuantityValidator;
use Tourze\Web3PHP\Validators\StringValidator;

class UnlockAccount extends EthMethod
{
    /**
     * 验证器
     *
     * @var array<int, mixed>
     */
    protected $validators = [
        AddressValidator::class, StringValidator::class, QuantityValidator::class,
    ];

    /**
     * 输入格式化器
     *
     * @var array<int, mixed>
     */
    protected $inputFormatters = [
        AddressFormatter::class, StringFormatter::class, NumberFormatter::class,
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
        2 => 300,
    ];

    /**
     * 构造函数
     *
     * @param string $method
     * @param array<int, mixed> $arguments
     */
    public function __construct(string $method = '', array $arguments = [])
    {
        parent::__construct('' !== $method ? $method : 'personal_unlockAccount', $arguments);
    }
}
