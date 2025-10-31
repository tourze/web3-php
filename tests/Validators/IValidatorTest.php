<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Validators;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Validators\IValidator;

/**
 * IValidator 接口测试
 * @internal
 */
#[CoversClass(IValidator::class)]
final class IValidatorTest extends TestCase
{
    /**
     * 测试IValidator是一个接口
     */
    public function testIsInterface(): void
    {
        $reflection = new \ReflectionClass(IValidator::class);
        $this->assertTrue($reflection->isInterface());
    }

    /**
     * 测试IValidator定义了validate方法
     */
    public function testHasValidateMethod(): void
    {
        $reflection = new \ReflectionClass(IValidator::class);
        $this->assertTrue($reflection->hasMethod('validate'));
    }

    /**
     * 测试validate方法的签名
     */
    public function testValidateMethodSignature(): void
    {
        $reflection = new \ReflectionMethod(IValidator::class, 'validate');

        // 检查方法是公共的
        $this->assertTrue($reflection->isPublic());

        // 检查方法是静态的
        $this->assertTrue($reflection->isStatic());

        // 检查方法参数
        $parameters = $reflection->getParameters();
        $this->assertCount(1, $parameters);
        $this->assertSame('value', $parameters[0]->getName());

        // 检查返回类型 - 接口方法可能没有返回类型声明
        $returnType = $reflection->getReturnType();
        // 在接口中，返回类型可能是 null，这是正常的
        if (null !== $returnType) {
            if ($returnType instanceof \ReflectionNamedType) {
                $this->assertSame('bool', (string) $returnType);
            } else {
                self::fail('Return type should be ReflectionNamedType');
            }
        }
    }

    /**
     * 测试实现IValidator的类
     */
    public function testImplementation(): void
    {
        // 创建一个简单的实现类来测试接口
        $implementation = new class implements IValidator {
            public static function validate($value): bool
            {
                return is_string($value);
            }
        };

        $this->assertInstanceOf(IValidator::class, $implementation);
        $this->assertTrue($implementation::validate('test'));
        $this->assertFalse($implementation::validate(123));
    }

    /**
     * 测试接口命名空间
     */
    public function testNamespace(): void
    {
        $reflection = new \ReflectionClass(IValidator::class);
        $this->assertSame('Tourze\Web3PHP\Validators', $reflection->getNamespaceName());
    }

    /**
     * 测试接口文档注释
     */
    public function testInterfaceDocumentation(): void
    {
        $reflection = new \ReflectionClass(IValidator::class);
        $comment = $reflection->getDocComment();

        // 接口可能没有文档注释，这是正常的
        if (false !== $comment) {
            $this->assertIsString($comment);
            $this->assertStringContainsString('验证', $comment);
            $this->assertStringContainsString('@param mixed $value', $comment);
            $this->assertStringContainsString('@return bool', $comment);
        } else {
            // 如果没有文档注释，至少确保方法有文档注释
            $method = $reflection->getMethod('validate');
            $methodComment = $method->getDocComment();
            $this->assertIsString($methodComment);
            $this->assertStringContainsString('验证', $methodComment);
            $this->assertStringContainsString('@param mixed $value', $methodComment);
            $this->assertStringContainsString('@return bool', $methodComment);
        }
    }
}
