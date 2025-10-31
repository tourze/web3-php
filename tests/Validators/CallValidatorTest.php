<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Validators;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Validators\CallValidator;

/**
 * CallValidator 测试
 * @internal
 */
#[CoversClass(CallValidator::class)]
final class CallValidatorTest extends TestCase
{
    /**
     * 测试验证有效值
     */
    public function testValidateValidValues(): void
    {
        $this->assertTrue(CallValidator::validate(['to' => '0x1234567890123456789012345678901234567890']));
        $this->assertTrue(CallValidator::validate(['from' => '0x1234567890123456789012345678901234567890', 'to' => '0x0987654321098765432109876543210987654321']));
        $this->assertTrue(CallValidator::validate(['to' => '0x1234567890123456789012345678901234567890', 'data' => '0xabcd']));
    }

    /**
     * 测试验证无效值
     */
    public function testValidateInvalidValues(): void
    {
        // 测试验证器应该拒绝无效值
        $invalidValues = ['not an object', 123, null, true];

        foreach ($invalidValues as $value) {
            try {
                $result = CallValidator::validate($value);
                // 如果没有抛出异常，应该返回 false
                $this->assertFalse($result, '验证器应该拒绝无效值: ' . print_r($value, true));
            } catch (\TypeError $e) {
                // 如果抛出类型错误，说明验证器正确拒绝了无效值
                $this->assertStringContainsString('must be of type array', $e->getMessage());
            }
        }
    }

    /**
     * 测试validate方法是静态的
     */
    public function testValidateMethodIsStatic(): void
    {
        $reflection = new \ReflectionMethod(CallValidator::class, 'validate');
        $this->assertTrue($reflection->isStatic());
    }

    /**
     * 测试validate方法返回类型
     */
    public function testValidateMethodReturnType(): void
    {
        $reflection = new \ReflectionMethod(CallValidator::class, 'validate');
        $returnType = $reflection->getReturnType();
        // 验证方法现在有bool返回类型声明
        $this->assertNotNull($returnType);
        if ($returnType instanceof \ReflectionNamedType) {
            $this->assertEquals('bool', $returnType->getName());
        } else {
            $this->assertEquals('bool', (string) $returnType);
        }
    }
}
