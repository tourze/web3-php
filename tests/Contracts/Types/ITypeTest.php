<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Contracts\Types;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Contracts\Types\IType;

/**
 * IType 接口测试
 * @internal
 */
#[CoversClass(IType::class)]
final class ITypeTest extends TestCase
{
    /**
     * 测试IType是一个接口
     */
    public function testIsInterface(): void
    {
        $reflection = new \ReflectionClass(IType::class);
        $this->assertTrue($reflection->isInterface());
    }

    /**
     * 测试IType定义了isType方法
     */
    public function testHasIsTypeMethod(): void
    {
        $reflection = new \ReflectionClass(IType::class);
        $this->assertTrue($reflection->hasMethod('isType'));
    }

    /**
     * 测试IType定义了isDynamicType方法
     */
    public function testHasIsDynamicTypeMethod(): void
    {
        $reflection = new \ReflectionClass(IType::class);
        $this->assertTrue($reflection->hasMethod('isDynamicType'));
    }

    /**
     * 测试IType定义了inputFormat方法
     */
    public function testHasInputFormatMethod(): void
    {
        $reflection = new \ReflectionClass(IType::class);
        $this->assertTrue($reflection->hasMethod('inputFormat'));
    }

    /**
     * 测试isType方法的签名
     */
    public function testIsTypeMethodSignature(): void
    {
        $reflection = new \ReflectionMethod(IType::class, 'isType');

        // 检查方法是公共的
        $this->assertTrue($reflection->isPublic());

        // 检查方法参数
        $parameters = $reflection->getParameters();
        $this->assertCount(1, $parameters);
        $this->assertSame('name', $parameters[0]->getName());

        // 检查返回类型
        $returnType = $reflection->getReturnType();
        if (null !== $returnType) {
            $this->assertInstanceOf(\ReflectionNamedType::class, $returnType);
            $this->assertSame('bool', (string) $returnType);
        }
    }

    /**
     * 测试isDynamicType方法的签名
     */
    public function testIsDynamicTypeMethodSignature(): void
    {
        $reflection = new \ReflectionMethod(IType::class, 'isDynamicType');

        // 检查方法是公共的
        $this->assertTrue($reflection->isPublic());

        // 检查方法没有参数
        $parameters = $reflection->getParameters();
        $this->assertCount(0, $parameters);

        // 检查返回类型
        $returnType = $reflection->getReturnType();
        if (null !== $returnType) {
            $this->assertInstanceOf(\ReflectionNamedType::class, $returnType);
            $this->assertSame('bool', (string) $returnType);
        }
    }

    /**
     * 测试inputFormat方法的签名
     */
    public function testInputFormatMethodSignature(): void
    {
        $reflection = new \ReflectionMethod(IType::class, 'inputFormat');

        // 检查方法是公共的
        $this->assertTrue($reflection->isPublic());

        // 检查方法参数
        $parameters = $reflection->getParameters();
        $this->assertCount(2, $parameters);
        $this->assertSame('value', $parameters[0]->getName());
        $this->assertSame('name', $parameters[1]->getName());

        // 检查返回类型
        $returnType = $reflection->getReturnType();
        if (null !== $returnType) {
            $this->assertInstanceOf(\ReflectionNamedType::class, $returnType);
            $this->assertSame('string', (string) $returnType);
        }
    }

    /**
     * 测试实现IValidator的类
     */
    public function testImplementation(): void
    {
        // 创建一个简单的实现类来测试接口
        $implementation = new class implements IType {
            public function isType($name): bool
            {
                return 'test' === $name;
            }

            public function isDynamicType(): bool
            {
                return false;
            }

            public function inputFormat($value, $name): string
            {
                return 'formatted_' . $value;
            }
        };

        $this->assertInstanceOf(IType::class, $implementation);
        $this->assertTrue($implementation->isType('test'));
        $this->assertFalse($implementation->isType('other'));
        $this->assertFalse($implementation->isDynamicType());
        $this->assertSame('formatted_value', $implementation->inputFormat('value', 'test'));
    }

    /**
     * 测试接口命名空间
     */
    public function testNamespace(): void
    {
        $reflection = new \ReflectionClass(IType::class);
        $this->assertSame('Tourze\Web3PHP\Contracts\Types', $reflection->getNamespaceName());
    }

    /**
     * 测试接口文档注释
     */
    public function testInterfaceDocumentation(): void
    {
        $reflection = new \ReflectionClass(IType::class);
        $comment = $reflection->getDocComment();

        // 检查类是否有文档注释
        if (false !== $comment) {
            $this->assertIsString($comment);
            $this->assertStringContainsString('web3.php', $comment);
        } else {
            // 如果类没有文档注释，检查文件是否有文档注释
            $fileName = $reflection->getFileName();
            if (false === $fileName) {
                self::fail('Unable to get file name for class: ' . IType::class);
            }
            $this->assertFileExists($fileName);
            $content = file_get_contents($fileName);
            if (false === $content) {
                self::fail('Unable to read file: ' . $fileName);
            }
            $this->assertStringContainsString('web3.php', $content);
            $this->assertStringContainsString('@author', $content);
        }
    }
}
