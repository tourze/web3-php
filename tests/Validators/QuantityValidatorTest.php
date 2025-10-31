<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Validators;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Validators\IValidator;
use Tourze\Web3PHP\Validators\QuantityValidator;

/**
 * QuantityValidator 测试
 * @internal
 */
#[CoversClass(QuantityValidator::class)]
final class QuantityValidatorTest extends TestCase
{
    /**
     * 测试验证有效值
     */
    public function testValidateValidValues(): void
    {
        $this->assertTrue(QuantityValidator::validate('0x1'));
        $this->assertTrue(QuantityValidator::validate('0x0'));
        $this->assertTrue(QuantityValidator::validate('0xff'));
    }

    /**
     * 测试验证无效值
     */
    public function testValidateInvalidValues(): void
    {
        $this->assertFalse(QuantityValidator::validate('not hex'));
        // QuantityValidator 接受整数和浮点数
        $this->assertTrue(QuantityValidator::validate(123));
        $this->assertTrue(QuantityValidator::validate(123.45));
        try {
            $this->assertFalse(QuantityValidator::validate(null));
        } catch (\TypeError $e) {
            $this->assertInstanceOf(\TypeError::class, $e);
        }
        // '0x' 是有效的十六进制数量（代表 0）
        $this->assertTrue(QuantityValidator::validate('0x'));
    }

    /**
     * 测试validate方法是静态的
     */
    public function testValidateMethodIsStatic(): void
    {
        $reflection = new \ReflectionMethod(QuantityValidator::class, 'validate');
        $this->assertTrue($reflection->isStatic());
    }

    /**
     * 测试validate方法返回类型
     */
    public function testValidateMethodReturnType(): void
    {
        $reflection = new \ReflectionMethod(QuantityValidator::class, 'validate');
        $returnType = $reflection->getReturnType();
        // validate 方法应该有 bool 返回类型声明
        $this->assertNotNull($returnType);
        $this->assertSame('bool', (string) $returnType);
    }
}
