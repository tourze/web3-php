<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Validators;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Validators\IValidator;
use Tourze\Web3PHP\Validators\ShhFilterValidator;

/**
 * ShhFilterValidator 测试
 * @internal
 */
#[CoversClass(ShhFilterValidator::class)]
final class ShhFilterValidatorTest extends TestCase
{
    /**
     * 测试验证有效值
     */
    public function testValidateValidValues(): void
    {
        $validFilter = [
            'topics' => ['0x123'],
        ];
        $this->assertTrue(ShhFilterValidator::validate($validFilter));

        $validFilterWithTo = [
            'to' => '0xabc',
            'topics' => ['0x123'],
        ];
        $this->assertTrue(ShhFilterValidator::validate($validFilterWithTo));

        $validFilterWithNullTopic = [
            'topics' => ['0x123', null],
        ];
        $this->assertTrue(ShhFilterValidator::validate($validFilterWithNullTopic));
    }

    /**
     * 测试验证无效值
     */
    public function testValidateInvalidValues(): void
    {
        try {
            $this->assertFalse(ShhFilterValidator::validate('not an object'));
        } catch (\TypeError $e) {
            $this->assertInstanceOf(\TypeError::class, $e);
        }
        try {
            $this->assertFalse(ShhFilterValidator::validate(123));
        } catch (\TypeError $e) {
            $this->assertInstanceOf(\TypeError::class, $e);
        }
        try {
            $this->assertFalse(ShhFilterValidator::validate(null));
        } catch (\TypeError $e) {
            $this->assertInstanceOf(\TypeError::class, $e);
        }
    }

    /**
     * 测试validate方法是静态的
     */
    public function testValidateMethodIsStatic(): void
    {
        $reflection = new \ReflectionMethod(ShhFilterValidator::class, 'validate');
        $this->assertTrue($reflection->isStatic());
    }

    /**
     * 测试validate方法返回类型
     */
    public function testValidateMethodReturnType(): void
    {
        $reflection = new \ReflectionMethod(ShhFilterValidator::class, 'validate');
        $returnType = $reflection->getReturnType();
        // validate 方法现在有返回类型声明: bool
        $this->assertInstanceOf(\ReflectionNamedType::class, $returnType);
        $this->assertSame('bool', $returnType->getName());
    }
}
