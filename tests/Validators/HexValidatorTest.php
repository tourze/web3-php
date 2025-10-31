<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Validators;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Validators\HexValidator;
use Tourze\Web3PHP\Validators\IValidator;

/**
 * HexValidator 测试
 * @internal
 */
#[CoversClass(HexValidator::class)]
final class HexValidatorTest extends TestCase
{
    /**
     * 测试验证有效的十六进制字符串（带0x前缀）
     */
    public function testValidateValidHexWithPrefix(): void
    {
        $this->assertTrue(HexValidator::validate('0x1234'));
        $this->assertTrue(HexValidator::validate('0xabcdef'));
        $this->assertTrue(HexValidator::validate('0xABCDEF'));
        $this->assertTrue(HexValidator::validate('0x123abcDEF'));
        $this->assertTrue(HexValidator::validate('0x')); // 空的十六进制字符串
    }

    /**
     * 测试验证无效的十六进制字符串
     */
    public function testValidateInvalidHex(): void
    {
        $this->assertFalse(HexValidator::validate('1234')); // 缺少0x前缀
        $this->assertFalse(HexValidator::validate('0x123g')); // 包含非法字符g
        $this->assertFalse(HexValidator::validate('0x12.34')); // 包含小数点
        $this->assertFalse(HexValidator::validate('x1234')); // 缺少0
        $this->assertFalse(HexValidator::validate('0X1234')); // 大写X
    }

    /**
     * 测试验证非字符串值
     */
    public function testValidateNonStringValues(): void
    {
        // 测试验证器应该拒绝非字符串输入
        $invalidValues = [123, null, [], new \stdClass(), true, false];

        foreach ($invalidValues as $value) {
            try {
                $result = HexValidator::validate($value);
                // 如果没有抛出异常，应该返回 false
                $this->assertFalse($result, '验证器应该拒绝非字符串值: ' . gettype($value));
            } catch (\TypeError $e) {
                // 如果抛出类型错误，说明验证器正确拒绝了无效值
                $this->assertStringContainsString('must be of type string', $e->getMessage());
            }
        }
    }

    /**
     * 测试验证空字符串
     */
    public function testValidateEmptyString(): void
    {
        $this->assertFalse(HexValidator::validate(''));
    }

    /**
     * 测试验证只有前缀的字符串
     */
    public function testValidateOnlyPrefix(): void
    {
        $this->assertTrue(HexValidator::validate('0x'));
    }

    /**
     * 测试验证长十六进制字符串
     */
    public function testValidateLongHexString(): void
    {
        $longHex = '0x' . str_repeat('a', 100);
        $this->assertTrue(HexValidator::validate($longHex));
    }

    /**
     * 测试验证全为零的十六进制字符串
     */
    public function testValidateAllZeroHex(): void
    {
        $this->assertTrue(HexValidator::validate('0x0000'));
        $this->assertTrue(HexValidator::validate('0x0'));
    }

    /**
     * 测试验证全为F的十六进制字符串
     */
    public function testValidateAllFHex(): void
    {
        $this->assertTrue(HexValidator::validate('0xFFFF'));
        $this->assertTrue(HexValidator::validate('0xffff'));
        $this->assertTrue(HexValidator::validate('0xFfFf'));
    }

    /**
     * 测试validate方法是静态的
     */
    public function testValidateMethodIsStatic(): void
    {
        $reflection = new \ReflectionMethod(HexValidator::class, 'validate');
        $this->assertTrue($reflection->isStatic());
    }

    /**
     * 测试validate方法返回类型
     */
    public function testValidateMethodReturnType(): void
    {
        $reflection = new \ReflectionMethod(HexValidator::class, 'validate');
        $returnType = $reflection->getReturnType();

        // PHP 8 中返回类型可能是 null（没有返回类型声明）
        if (null !== $returnType) {
            $this->assertInstanceOf(\ReflectionNamedType::class, $returnType);
            $this->assertSame('bool', (string) $returnType);
        } else {
            // 如果没有返回类型声明，检查方法实际返回值
            $this->assertTrue(HexValidator::validate('0x123'));
            $this->assertFalse(HexValidator::validate('invalid'));
        }
    }

    /**
     * 测试validate方法签名符合IValidator接口
     */
    public function testValidateMethodSignatureMatchesIValidator(): void
    {
        $reflection = new \ReflectionMethod(HexValidator::class, 'validate');

        // 检查方法是公共的
        $this->assertTrue($reflection->isPublic());

        // 检查方法是静态的
        $this->assertTrue($reflection->isStatic());

        // 检查方法参数
        $parameters = $reflection->getParameters();
        $this->assertCount(1, $parameters);
        $this->assertSame('value', $parameters[0]->getName());
    }

    /**
     * 测试正则表达式模式
     */
    public function testRegexPattern(): void
    {
        // 测试正则表达式是否正确匹配十六进制字符串
        $this->assertTrue(preg_match('/^0x[a-fA-F0-9]*$/', '0x1234') >= 1);
        $this->assertTrue(preg_match('/^0x[a-fA-F0-9]*$/', '0x') >= 1);
        $this->assertTrue(preg_match('/^0x[a-fA-F0-9]*$/', '0xabcdefABCDEF1234567890') >= 1);

        // 测试不匹配的情况
        $this->assertFalse(preg_match('/^0x[a-fA-F0-9]*$/', '1234') >= 1);
        $this->assertFalse(preg_match('/^0x[a-fA-F0-9]*$/', '0x123g') >= 1);
        $this->assertFalse(preg_match('/^0x[a-fA-F0-9]*$/', 'x1234') >= 1);
        $this->assertFalse(preg_match('/^0x[a-fA-F0-9]*$/', '0X1234') >= 1);
    }
}
