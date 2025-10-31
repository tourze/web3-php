<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Formatters;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Formatters\IFormatter;

/**
 * IFormatter 接口测试
 * @internal
 */
#[CoversClass(IFormatter::class)]
final class IFormatterTest extends TestCase
{
    /**
     * 测试IFormatter是一个接口
     */
    public function testIsInterface(): void
    {
        $reflection = new \ReflectionClass(IFormatter::class);
        $this->assertTrue($reflection->isInterface());
    }

    /**
     * 测试IFormatter定义了format方法
     */
    public function testHasFormatMethod(): void
    {
        $reflection = new \ReflectionClass(IFormatter::class);
        $this->assertTrue($reflection->hasMethod('format'));
    }

    /**
     * 测试format方法的签名
     */
    public function testFormatMethodSignature(): void
    {
        $reflection = new \ReflectionMethod(IFormatter::class, 'format');

        // 检查方法是公共的
        $this->assertTrue($reflection->isPublic());

        // 检查方法是静态的
        $this->assertTrue($reflection->isStatic());

        // 检查方法参数
        $parameters = $reflection->getParameters();
        $this->assertCount(1, $parameters);
        $this->assertSame('value', $parameters[0]->getName());

        // 检查返回类型
        $returnType = $reflection->getReturnType();
        if (null !== $returnType) {
            $this->assertInstanceOf(\ReflectionNamedType::class, $returnType);
            $this->assertSame('mixed', (string) $returnType);
        }
    }

    /**
     * 测试实现IFormatter的类
     */
    public function testImplementation(): void
    {
        // 创建一个简单的实现类来测试接口
        $implementation = new class implements IFormatter {
            public static function format($value): mixed
            {
                return $value;
            }
        };

        $this->assertInstanceOf(IFormatter::class, $implementation);
        $this->assertSame('test', $implementation::format('test'));
    }

    /**
     * 测试接口命名空间
     */
    public function testNamespace(): void
    {
        $reflection = new \ReflectionClass(IFormatter::class);
        $this->assertSame('Tourze\Web3PHP\Formatters', $reflection->getNamespaceName());
    }
}
