<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Validators;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Validators\FilterValidator;

/**
 * FilterValidator 测试
 * @internal
 */
#[CoversClass(FilterValidator::class)]
final class FilterValidatorTest extends TestCase
{
    /**
     * 测试验证有效值
     */
    public function testValidateValidValues(): void
    {
        $this->assertTrue(FilterValidator::validate([]));
        $this->assertTrue(FilterValidator::validate(['fromBlock' => 'latest']));
        $this->assertTrue(FilterValidator::validate(['toBlock' => '0x123']));
        $this->assertTrue(FilterValidator::validate(['address' => '0x1234567890123456789012345678901234567890']));
        $this->assertTrue(FilterValidator::validate(['topics' => ['0xabcd']]));
    }

    /**
     * 测试验证无效值
     */
    public function testValidateInvalidValues(): void
    {
        $this->assertFalse(FilterValidator::validate('not an object'));
        $this->assertFalse(FilterValidator::validate(123));
        $this->assertFalse(FilterValidator::validate(null));
        $this->assertFalse(FilterValidator::validate(true));
    }

    /**
     * 测试验证无效地址
     */
    public function testValidateInvalidAddress(): void
    {
        $this->assertFalse(FilterValidator::validate(['address' => 'invalid_address']));
        $this->assertFalse(FilterValidator::validate(['address' => ['invalid_address']]));
    }

    /**
     * 测试验证无效主题
     */
    public function testValidateInvalidTopics(): void
    {
        $this->assertFalse(FilterValidator::validate(['topics' => ['invalid_topic']]));
        $this->assertFalse(FilterValidator::validate(['topics' => [['invalid_topic']]]));
    }

    /**
     * 测试validate方法是静态的
     */
    public function testValidateMethodIsStatic(): void
    {
        $reflection = new \ReflectionMethod(FilterValidator::class, 'validate');
        $this->assertTrue($reflection->isStatic());
    }

    /**
     * 测试validate方法返回类型
     */
    public function testValidateMethodReturnType(): void
    {
        // 验证方法返回布尔值
        $this->assertIsBool(FilterValidator::validate([]));
        $this->assertIsBool(FilterValidator::validate('invalid'));
    }

    /**
     * 测试validate方法签名符合IValidator接口
     */
    public function testValidateMethodSignatureMatchesIValidator(): void
    {
        $reflection = new \ReflectionMethod(FilterValidator::class, 'validate');

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
