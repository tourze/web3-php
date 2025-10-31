<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Validators;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Validators\BlockHashValidator;
use Tourze\Web3PHP\Validators\IValidator;

/**
 * BlockHashValidator 测试
 * @internal
 */
#[CoversClass(BlockHashValidator::class)]
final class BlockHashValidatorTest extends TestCase
{
    /**
     * 测试验证有效的区块哈希（带0x前缀）
     */
    public function testValidateValidHashWithPrefix(): void
    {
        $validHash = '0x1234567890abcdef1234567890abcdef1234567890abcdef1234567890abcdef';
        $this->assertTrue(BlockHashValidator::validate($validHash));
    }

    /**
     * 测试验证有效的区块哈希（大写带0x前缀）
     */
    public function testValidateValidHashWithPrefixUppercase(): void
    {
        $validHash = '0x1234567890ABCDEF1234567890ABCDEF1234567890ABCDEF1234567890ABCDEF';
        $this->assertTrue(BlockHashValidator::validate($validHash));
    }

    /**
     * 测试验证无效的区块哈希（不带0x前缀）
     */
    public function testValidateInvalidHashWithoutPrefix(): void
    {
        $invalidHash = '1234567890abcdef1234567890abcdef1234567890abcdef1234567890abcdef';
        $this->assertFalse(BlockHashValidator::validate($invalidHash));
    }

    /**
     * 测试验证无效的区块哈希（长度不足）
     */
    public function testValidateInvalidHashTooShort(): void
    {
        $invalidHash = '0x1234567890abcdef';
        $this->assertFalse(BlockHashValidator::validate($invalidHash));
    }

    /**
     * 测试验证无效的区块哈希（长度过长）
     */
    public function testValidateInvalidHashTooLong(): void
    {
        $invalidHash = '0x1234567890abcdef1234567890abcdef1234567890abcdef1234567890abcdef1234';
        $this->assertFalse(BlockHashValidator::validate($invalidHash));
    }

    /**
     * 测试验证无效的区块哈希（包含非法字符）
     */
    public function testValidateInvalidHashWithInvalidChars(): void
    {
        $invalidHash = '0x1234567890abcdef1234567890abcdef1234567890abcdef1234567890abcdeg';
        $this->assertFalse(BlockHashValidator::validate($invalidHash));
    }

    /**
     * 测试验证空字符串
     */
    public function testValidateEmptyString(): void
    {
        $this->assertFalse(BlockHashValidator::validate(''));
    }

    /**
     * 测试验证null值
     */
    public function testValidateNull(): void
    {
        // 测试验证器应该拒绝null值
        try {
            $result = BlockHashValidator::validate(null);
            // 如果没有抛出异常，应该返回 false
            $this->assertFalse($result, '验证器应该拒绝null值');
        } catch (\TypeError $e) {
            // 如果抛出类型错误，说明验证器正确拒绝了null值
            $this->assertStringContainsString('must be of type string', $e->getMessage());
        }
    }

    /**
     * 测试验证整数
     */
    public function testValidateInteger(): void
    {
        // 测试验证器应该拒绝整数
        try {
            $result = BlockHashValidator::validate(123456789);
            // 如果没有抛出异常，应该返回 false
            $this->assertFalse($result, '验证器应该拒绝整数');
        } catch (\TypeError $e) {
            // 如果抛出类型错误，说明验证器正确拒绝了整数
            $this->assertStringContainsString('must be of type string', $e->getMessage());
        }
    }

    /**
     * 测试验证数组
     */
    public function testValidateArray(): void
    {
        // 测试验证器应该拒绝数组
        try {
            $result = BlockHashValidator::validate([]);
            // 如果没有抛出异常，应该返回 false
            $this->assertFalse($result, '验证器应该拒绝数组');
        } catch (\TypeError $e) {
            // 如果抛出类型错误，说明验证器正确拒绝了数组
            $this->assertStringContainsString('must be of type string', $e->getMessage());
        }
    }

    /**
     * 测试验证对象
     */
    public function testValidateObject(): void
    {
        // 测试验证器应该拒绝对象
        try {
            $result = BlockHashValidator::validate(new \stdClass());
            // 如果没有抛出异常，应该返回 false
            $this->assertFalse($result, '验证器应该拒绝对象');
        } catch (\TypeError $e) {
            // 如果抛出类型错误，说明验证器正确拒绝了对象
            $this->assertStringContainsString('must be of type string', $e->getMessage());
        }
    }

    /**
     * 测试验证布尔值
     */
    public function testValidateBoolean(): void
    {
        // 测试验证器应该拒绝布尔值
        try {
            $result = BlockHashValidator::validate(true);
            // 如果没有抛出异常，应该返回 false
            $this->assertFalse($result, '验证器应该拒绝布尔值true');
        } catch (\TypeError $e) {
            // 如果抛出类型错误，说明验证器正确拒绝了布尔值
            $this->assertStringContainsString('must be of type string', $e->getMessage());
        }

        try {
            $result = BlockHashValidator::validate(false);
            // 如果没有抛出异常，应该返回 false
            $this->assertFalse($result, '验证器应该拒绝布尔值false');
        } catch (\TypeError $e) {
            // 如果抛出类型错误，说明验证器正确拒绝了布尔值
            $this->assertStringContainsString('must be of type string', $e->getMessage());
        }
    }

    /**
     * 测试validate方法是静态的
     */
    public function testValidateMethodIsStatic(): void
    {
        $reflection = new \ReflectionMethod(BlockHashValidator::class, 'validate');
        $this->assertTrue($reflection->isStatic());
    }

    /**
     * 测试validate方法返回类型
     */
    public function testValidateMethodReturnType(): void
    {
        $reflection = new \ReflectionMethod(BlockHashValidator::class, 'validate');
        $returnType = $reflection->getReturnType();
        // 由于 validate 方法没有返回类型声明，这里应该为 null
        $this->assertNull($returnType);
    }

    /**
     * 测试验证正则表达式模式
     */
    public function testRegexPattern(): void
    {
        // 测试正则表达式是否正确匹配64位十六进制字符串
        $this->assertTrue(preg_match('/^0x[a-fA-F0-9]{64}$/', '0x' . str_repeat('a', 64)) >= 1);
        $this->assertTrue(preg_match('/^0x[a-fA-F0-9]{64}$/', '0x' . str_repeat('0', 64)) >= 1);
        $this->assertTrue(preg_match('/^0x[a-fA-F0-9]{64}$/', '0x' . str_repeat('F', 64)) >= 1);

        // 测试不匹配的情况
        $this->assertFalse(preg_match('/^0x[a-fA-F0-9]{64}$/', '0x' . str_repeat('a', 63)) >= 1);
        $this->assertFalse(preg_match('/^0x[a-fA-F0-9]{64}$/', '0x' . str_repeat('a', 65)) >= 1);
        $this->assertFalse(preg_match('/^0x[a-fA-F0-9]{64}$/', '0x' . str_repeat('g', 64)) >= 1);
    }
}
