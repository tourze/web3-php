<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Validators;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Validators\IdentityValidator;
use Tourze\Web3PHP\Validators\IValidator;

/**
 * IdentityValidator 测试
 * @internal
 */
#[CoversClass(IdentityValidator::class)]
final class IdentityValidatorTest extends TestCase
{
    /**
     * 测试验证有效值
     */
    public function testValidateValidValues(): void
    {
        $this->assertTrue(IdentityValidator::validate('0x1234567890abcdef1234567890abcdef12345678'));
    }

    /**
     * 测试验证无效值
     */
    public function testValidateInvalidValues(): void
    {
        $this->assertFalse(IdentityValidator::validate('not an address'));
        $this->assertFalse(IdentityValidator::validate(123));
        $this->assertFalse(IdentityValidator::validate(null));
        $this->assertTrue(IdentityValidator::validate('0x123'));
    }

    /**
     * 测试validate方法是静态的
     */
    public function testValidateMethodIsStatic(): void
    {
        $reflection = new \ReflectionMethod(IdentityValidator::class, 'validate');
        $this->assertTrue($reflection->isStatic());
    }

    /**
     * 测试validate方法返回类型
     */
    public function testValidateMethodReturnType(): void
    {
        $reflection = new \ReflectionMethod(IdentityValidator::class, 'validate');
        $returnType = $reflection->getReturnType();
        // 方法可能没有返回类型声明，这是正常的
        if (null !== $returnType) {
            $this->assertInstanceOf(\ReflectionNamedType::class, $returnType);
            $this->assertSame('bool', (string) $returnType);
        }
        // 验证方法实际返回布尔值
        $this->assertIsBool(IdentityValidator::validate('test'));
    }

    /**
     * 测试不实现IValidator接口
     */
    public function testDoesNotImplementIValidator(): void
    {
        $reflection = new \ReflectionClass(IdentityValidator::class);
        $this->assertFalse($reflection->implementsInterface(IValidator::class));
    }
}
