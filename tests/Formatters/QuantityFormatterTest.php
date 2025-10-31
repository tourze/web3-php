<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Formatters;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Formatters\QuantityFormatter;

/**
 * QuantityFormatter 测试
 * @internal
 */
#[CoversClass(QuantityFormatter::class)]
final class QuantityFormatterTest extends TestCase
{
    /**
     * 测试格式化整数
     */
    public function testFormatInteger(): void
    {
        $result = QuantityFormatter::format(42);
        $this->assertIsString($result);
        $this->assertStringStartsWith('0x', $result);
    }

    /**
     * 测试格式化字符串
     */
    public function testFormatString(): void
    {
        $result = QuantityFormatter::format('123');
        $this->assertIsString($result);
        $this->assertStringStartsWith('0x', $result);
    }

    /**
     * 测试格式化十六进制字符串
     */
    public function testFormatHexString(): void
    {
        $result = QuantityFormatter::format('0xff');
        $this->assertIsString($result);
        $this->assertStringStartsWith('0x', $result);
    }

    /**
     * 测试格式化零
     */
    public function testFormatZero(): void
    {
        $result = QuantityFormatter::format(0);
        $this->assertIsString($result);
        $this->assertEquals('0x0', $result);
    }

    /**
     * 测试格式化大数
     */
    public function testFormatLargeNumber(): void
    {
        $result = QuantityFormatter::format('1000000000000000000');
        $this->assertIsString($result);
        $this->assertStringStartsWith('0x', $result);
    }

    /**
     * 测试格式化负数
     */
    public function testFormatNegative(): void
    {
        $result = QuantityFormatter::format(-10);
        $this->assertIsString($result);
        $this->assertStringStartsWith('0x', $result);
    }
}
