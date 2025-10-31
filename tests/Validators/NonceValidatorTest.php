<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Validators;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Validators\IValidator;
use Tourze\Web3PHP\Validators\NonceValidator;

/**
 * NonceValidator 测试
 * @internal
 */
#[CoversClass(NonceValidator::class)]
final class NonceValidatorTest extends TestCase
{
    /**
     * 测试验证有效值
     */
    public function testValidateValidValues(): void
    {
        $this->assertTrue(NonceValidator::validate('0x1234567890abcdef'));
        $this->assertTrue(NonceValidator::validate('0x0000000000000000'));
        $this->assertTrue(NonceValidator::validate('0xffffffffffffffff'));
    }

    /**
     * 测试验证无效值
     */
    public function testValidateInvalidValues(): void
    {
        $this->assertFalse(NonceValidator::validate('not hex'));
        $this->assertFalse(NonceValidator::validate(123));
        $this->assertFalse(NonceValidator::validate(null));
        $this->assertFalse(NonceValidator::validate('0x123'));
    }

    /**
     * 测试validate方法是静态的
     */
    public function testValidateMethodIsStatic(): void
    {
        $reflection = new \ReflectionMethod(NonceValidator::class, 'validate');
        $this->assertTrue($reflection->isStatic());
    }

    /**
     * 测试validate方法返回类型
     */
    public function testValidateMethodReturnType(): void
    {
        $reflection = new \ReflectionMethod(NonceValidator::class, 'validate');
        $returnType = $reflection->getReturnType();
        // 由于 validate 方法没有返回类型声明，这里应该为 null
        $this->assertNull($returnType);
    }
}
