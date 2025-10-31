<?php

/**
 * 此文件是 web3.php 包的一部分。
 *
 * (c) Kuan-Cheng,Lai <alk03073135@gmail.com>
 *
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */

namespace Tourze\Web3PHP\Methods\Shh;

use Tourze\Web3PHP\Methods\EthMethod;
use Tourze\Web3PHP\Validators\IdentityValidator;

class AddToGroup extends EthMethod
{
    /**
     * 验证器
     *
     * @var array<int, mixed>
     */
    protected $validators = [
        IdentityValidator::class,
    ];

    /**
     * 输入格式化器
     *
     * @var array<int, mixed>
     */
    protected $inputFormatters = [];

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

    /**
     * 构造函数
     *
     * @param string $method
     * @param array<int, mixed> $arguments
     */
    public function __construct($method = '', array $arguments = [])
    {
        parent::__construct('' !== $method ? $method : 'shh_addToGroup', $arguments);
    }
}
