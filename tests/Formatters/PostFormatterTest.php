<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Formatters;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Formatters\IFormatter;
use Tourze\Web3PHP\Formatters\PostFormatter;

/**
 * PostFormatter 测试
 * @internal
 */
#[CoversClass(PostFormatter::class)]
final class PostFormatterTest extends TestCase
{
    /**
     * 测试格式化包含priority的数组
     */
    public function testFormatWithPriority(): void
    {
        $value = ['priority' => '0x64'];
        $result = PostFormatter::format($value);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('priority', $result);
        $this->assertSame('0x64', $result['priority']);
    }

    /**
     * 测试格式化包含ttl的数组
     */
    public function testFormatWithTtl(): void
    {
        $value = ['ttl' => '0x64'];
        $result = PostFormatter::format($value);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('ttl', $result);
        $this->assertSame('0x64', $result['ttl']);
    }

    /**
     * 测试格式化同时包含priority和ttl的数组
     */
    public function testFormatWithPriorityAndTtl(): void
    {
        $value = [
            'priority' => '0x64',
            'ttl' => '0x32',
            'other' => 'value',
        ];
        $result = PostFormatter::format($value);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('priority', $result);
        $this->assertArrayHasKey('ttl', $result);
        $this->assertArrayHasKey('other', $result);
        $this->assertSame('0x64', $result['priority']);
        $this->assertSame('0x32', $result['ttl']);
        $this->assertSame('value', $result['other']);
    }

    /**
     * 测试格式化不包含priority和ttl的数组
     */
    public function testFormatWithoutPriorityAndTtl(): void
    {
        $value = ['other' => 'value'];
        $result = PostFormatter::format($value);

        $this->assertIsArray($result);
        $this->assertSame($value, $result);
    }

    /**
     * 测试格式化空数组
     */
    public function testFormatEmptyArray(): void
    {
        $value = [];
        $result = PostFormatter::format($value);

        $this->assertIsArray($result);
        $this->assertSame([], $result);
    }

    /**
     * 测试实现IFormatter接口
     */
    public function testImplementsIFormatter(): void
    {
        $reflection = new \ReflectionClass(PostFormatter::class);
        $this->assertTrue($reflection->implementsInterface(IFormatter::class));
    }

    /**
     * 测试format方法是静态的
     */
    public function testFormatMethodIsStatic(): void
    {
        $reflection = new \ReflectionMethod(PostFormatter::class, 'format');
        $this->assertTrue($reflection->isStatic());
    }

    /**
     * 测试format方法返回类型
     */
    public function testFormatMethodReturnType(): void
    {
        $reflection = new \ReflectionMethod(PostFormatter::class, 'format');
        $returnType = $reflection->getReturnType();

        // 检查方法是否返回数组类型（通过调用方法验证）
        $this->assertIsArray($reflection->invoke(null, ['priority' => '0x1', 'ttl' => '0x2']));
    }
}
