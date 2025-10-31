<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Validators;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Validators\IValidator;
use Tourze\Web3PHP\Validators\PostValidator;

/**
 * PostValidator 测试
 * @internal
 */
#[CoversClass(PostValidator::class)]
final class PostValidatorTest extends TestCase
{
    /**
     * 测试验证有效值
     */
    public function testValidateValidValues(): void
    {
        $validPost = [
            'topics' => ['0x123'],
            'payload' => '0x456',
            'priority' => '0x1',
            'ttl' => '0x2',
        ];
        $this->assertTrue(PostValidator::validate($validPost));

        $validPostWithFromTo = [
            'from' => '0xabc',
            'to' => '0xdef',
            'topics' => ['0x123'],
            'payload' => '0x456',
            'priority' => '0x1',
            'ttl' => '0x2',
        ];
        $this->assertTrue(PostValidator::validate($validPostWithFromTo));
    }

    /**
     * 测试验证无效值
     */
    public function testValidateInvalidValues(): void
    {
        try {
            $this->assertFalse(PostValidator::validate('not an object'));
        } catch (\TypeError $e) {
            $this->assertInstanceOf(\TypeError::class, $e);
        }
        try {
            $this->assertFalse(PostValidator::validate(123));
        } catch (\TypeError $e) {
            $this->assertInstanceOf(\TypeError::class, $e);
        }
        try {
            $this->assertFalse(PostValidator::validate(null));
        } catch (\TypeError $e) {
            $this->assertInstanceOf(\TypeError::class, $e);
        }
    }

    /**
     * 测试validate方法是静态的
     */
    public function testValidateMethodIsStatic(): void
    {
        $reflection = new \ReflectionMethod(PostValidator::class, 'validate');
        $this->assertTrue($reflection->isStatic());
    }

    /**
     * 测试validate方法返回类型
     */
    public function testValidateMethodReturnType(): void
    {
        $reflection = new \ReflectionMethod(PostValidator::class, 'validate');
        $returnType = $reflection->getReturnType();
        // validate 方法现在有返回类型声明: bool
        $this->assertInstanceOf(\ReflectionNamedType::class, $returnType);
        $this->assertSame('bool', $returnType->getName());
    }
}
