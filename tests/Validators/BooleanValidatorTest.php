<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Validators;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Validators\BooleanValidator;
use Tourze\Web3PHP\Validators\IValidator;

/**
 * BooleanValidator 测试
 * @internal
 */
#[CoversClass(BooleanValidator::class)]
final class BooleanValidatorTest extends TestCase
{
    /**
     * 测试验证true值
     */
    public function testValidateTrue(): void
    {
        $this->assertTrue(BooleanValidator::validate(true));
    }

    /**
     * 测试验证false值
     */
    public function testValidateFalse(): void
    {
        $this->assertTrue(BooleanValidator::validate(false));
    }

    /**
     * 测试验证字符串"true"
     */
    public function testValidateStringTrue(): void
    {
        $this->assertFalse(BooleanValidator::validate('true'));
    }

    /**
     * 测试验证字符串"false"
     */
    public function testValidateStringFalse(): void
    {
        $this->assertFalse(BooleanValidator::validate('false'));
    }

    /**
     * 测试验证整数1
     */
    public function testValidateIntegerOne(): void
    {
        $this->assertFalse(BooleanValidator::validate(1));
    }

    /**
     * 测试验证整数0
     */
    public function testValidateIntegerZero(): void
    {
        $this->assertFalse(BooleanValidator::validate(0));
    }

    /**
     * 测试验证空字符串
     */
    public function testValidateEmptyString(): void
    {
        $this->assertFalse(BooleanValidator::validate(''));
    }

    /**
     * 测试验证null值
     */
    public function testValidateNull(): void
    {
        $this->assertFalse(BooleanValidator::validate(null));
    }

    /**
     * 测试验证数组
     */
    public function testValidateArray(): void
    {
        $this->assertFalse(BooleanValidator::validate([]));
    }

    /**
     * 测试验证对象
     */
    public function testValidateObject(): void
    {
        $this->assertFalse(BooleanValidator::validate(new \stdClass()));
    }

    /**
     * 测试validate方法是静态的
     */
    public function testValidateMethodIsStatic(): void
    {
        $reflection = new \ReflectionMethod(BooleanValidator::class, 'validate');
        $this->assertTrue($reflection->isStatic());
    }

    /**
     * 测试validate方法返回类型
     */
    public function testValidateMethodReturnType(): void
    {
        // 验证方法返回布尔值
        $this->assertIsBool(BooleanValidator::validate(true));
        $this->assertIsBool(BooleanValidator::validate(false));
        $this->assertIsBool(BooleanValidator::validate('true'));
    }

    /**
     * 测试validate方法签名符合IValidator接口
     */
    public function testValidateMethodSignatureMatchesIValidator(): void
    {
        $reflection = new \ReflectionMethod(BooleanValidator::class, 'validate');

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
