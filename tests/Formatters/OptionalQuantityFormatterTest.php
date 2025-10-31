<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Formatters;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Formatters\OptionalQuantityFormatter;

/**
 * OptionalQuantityFormatter 测试
 * @internal
 */
#[CoversClass(OptionalQuantityFormatter::class)]
final class OptionalQuantityFormatterTest extends TestCase
{
    /**
     * 测试格式化有效值
     */
    public function testFormatValidValue(): void
    {
        $result = OptionalQuantityFormatter::format('0x123');
        $this->assertIsString($result);
        $this->assertStringStartsWith('0x', $result);
    }

    /**
     * 测试格式化空值
     */
    public function testFormatNull(): void
    {
        $result = OptionalQuantityFormatter::format(null);
        $this->assertIsString($result);
        $this->assertEquals('0x0', $result);
    }

    /**
     * 测试格式化零
     */
    public function testFormatZero(): void
    {
        $result = OptionalQuantityFormatter::format(0);
        $this->assertIsString($result);
        $this->assertEquals('0x0', $result);
    }

    /**
     * 测试格式化整数
     */
    public function testFormatInteger(): void
    {
        $result = OptionalQuantityFormatter::format(42);
        $this->assertIsString($result);
        $this->assertStringStartsWith('0x', $result);
    }

    /**
     * 测试格式化字符串
     */
    public function testFormatString(): void
    {
        $result = OptionalQuantityFormatter::format('100');
        $this->assertIsString($result);
        $this->assertStringStartsWith('0x', $result);
    }
}
