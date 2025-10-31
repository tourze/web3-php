<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Validators;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Validators\IValidator;
use Tourze\Web3PHP\Validators\StringValidator;

/**
 * StringValidator 测试
 * @internal
 */
#[CoversClass(StringValidator::class)]
final class StringValidatorTest extends TestCase
{
    /**
     * 测试验证有效值
     */
    public function testValidateValidValues(): void
    {
        $this->assertTrue(StringValidator::validate('hello'));
        $this->assertTrue(StringValidator::validate(''));
        $this->assertTrue(StringValidator::validate('test'));
    }

    /**
     * 测试验证无效值
     */
    public function testValidateInvalidValues(): void
    {
        try {
            $this->assertFalse(StringValidator::validate(123));
        } catch (\TypeError $e) {
            $this->assertInstanceOf(\TypeError::class, $e);
        }
        try {
            $this->assertFalse(StringValidator::validate(null));
        } catch (\TypeError $e) {
            $this->assertInstanceOf(\TypeError::class, $e);
        }
        try {
            $this->assertFalse(StringValidator::validate([]));
        } catch (\TypeError $e) {
            $this->assertInstanceOf(\TypeError::class, $e);
        }
        try {
            $this->assertFalse(StringValidator::validate(new \stdClass()));
        } catch (\TypeError $e) {
            $this->assertInstanceOf(\TypeError::class, $e);
        }
    }

    /**
     * 测试validate方法是静态的
     */
    public function testValidateMethodIsStatic(): void
    {
        $reflection = new \ReflectionMethod(StringValidator::class, 'validate');
        $this->assertTrue($reflection->isStatic());
    }

    /**
     * 测试validate方法返回类型
     */
    public function testValidateMethodReturnType(): void
    {
        $reflection = new \ReflectionMethod(StringValidator::class, 'validate');
        $returnType = $reflection->getReturnType();
        // 由于 validate 方法没有返回类型声明，这里应该为 null
        $this->assertNull($returnType);
    }
}
