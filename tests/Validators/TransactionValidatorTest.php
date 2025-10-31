<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Validators;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Validators\IValidator;
use Tourze\Web3PHP\Validators\TransactionValidator;

/**
 * TransactionValidator 测试
 * @internal
 */
#[CoversClass(TransactionValidator::class)]
final class TransactionValidatorTest extends TestCase
{
    /**
     * 测试验证有效值
     */
    public function testValidateValidValues(): void
    {
        // 空数组没有 from 字段，应该返回 false
        $this->assertFalse(TransactionValidator::validate([]));
        // 有效的地址
        $this->assertTrue(TransactionValidator::validate(['from' => '0x71C7656EC7ab88b098defB751B7401B5f6d8976F']));
    }

    /**
     * 测试验证无效值
     */
    public function testValidateInvalidValues(): void
    {
        try {
            $this->assertFalse(TransactionValidator::validate('not an object'));
        } catch (\TypeError $e) {
            $this->assertInstanceOf(\TypeError::class, $e);
        }
        try {
            $this->assertFalse(TransactionValidator::validate(123));
        } catch (\TypeError $e) {
            $this->assertInstanceOf(\TypeError::class, $e);
        }
        try {
            $this->assertFalse(TransactionValidator::validate(null));
        } catch (\TypeError $e) {
            $this->assertInstanceOf(\TypeError::class, $e);
        }
    }

    /**
     * 测试validate方法是静态的
     */
    public function testValidateMethodIsStatic(): void
    {
        $reflection = new \ReflectionMethod(TransactionValidator::class, 'validate');
        $this->assertTrue($reflection->isStatic());
    }

    /**
     * 测试validate方法返回类型
     */
    public function testValidateMethodReturnType(): void
    {
        $reflection = new \ReflectionMethod(TransactionValidator::class, 'validate');
        $returnType = $reflection->getReturnType();
        // validate 方法现在有返回类型声明: bool
        $this->assertInstanceOf(\ReflectionNamedType::class, $returnType);
        $this->assertSame('bool', $returnType->getName());
    }

    /**
     * 测试实现IValidator接口
     */
    public function testImplementsIValidator(): void
    {
        $reflection = new \ReflectionClass(TransactionValidator::class);
        // TransactionValidator 没有实现 IValidator 接口
        $this->assertFalse($reflection->implementsInterface(IValidator::class));
    }

    /**
     * 测试validate方法签名符合IValidator接口
     */
    public function testValidateMethodSignatureMatchesIValidator(): void
    {
        $reflection = new \ReflectionMethod(TransactionValidator::class, 'validate');

        // 检查方法是公共的
        $this->assertTrue($reflection->isPublic());

        // 检查方法是静态的
        $this->assertTrue($reflection->isStatic());

        // 检查方法参数
        $parameters = $reflection->getParameters();
        $this->assertCount(1, $parameters);
        $this->assertSame('value', $parameters[0]->getName());
    }
}
