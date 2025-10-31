<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Validators;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Validators\IValidator;
use Tourze\Web3PHP\Validators\TagValidator;

/**
 * TagValidator 测试
 * @internal
 */
#[CoversClass(TagValidator::class)]
final class TagValidatorTest extends TestCase
{
    /**
     * 测试验证有效值
     */
    public function testValidateValidValues(): void
    {
        $this->assertTrue(TagValidator::validate('latest'));
        $this->assertTrue(TagValidator::validate('earliest'));
        $this->assertTrue(TagValidator::validate('pending'));
    }

    /**
     * 测试验证无效值
     */
    public function testValidateInvalidValues(): void
    {
        $this->assertFalse(TagValidator::validate('not a tag'));
        $this->assertFalse(TagValidator::validate(123));
        $this->assertFalse(TagValidator::validate(null));
        $this->assertFalse(TagValidator::validate('0x123'));
    }

    /**
     * 测试validate方法是静态的
     */
    public function testValidateMethodIsStatic(): void
    {
        $reflection = new \ReflectionMethod(TagValidator::class, 'validate');
        $this->assertTrue($reflection->isStatic());
    }

    /**
     * 测试validate方法返回类型
     */
    public function testValidateMethodReturnType(): void
    {
        $reflection = new \ReflectionMethod(TagValidator::class, 'validate');
        $returnType = $reflection->getReturnType();
        // 由于 validate 方法没有返回类型声明，这里应该为 null
        $this->assertNull($returnType);
    }
}
