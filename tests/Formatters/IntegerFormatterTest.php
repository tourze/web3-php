<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Formatters;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Formatters\IntegerFormatter;

/**
 * IntegerFormatter 测试
 * @internal
 */
#[CoversClass(IntegerFormatter::class)]
final class IntegerFormatterTest extends TestCase
{
    /**
     * 测试格式化整数
     */
    public function testFormatInteger(): void
    {
        $result = IntegerFormatter::format(42);
        $this->assertIsString($result);
        $this->assertEquals(64, strlen($result));
    }

    /**
     * 测试格式化字符串整数
     */
    public function testFormatStringInteger(): void
    {
        $result = IntegerFormatter::format('123');
        $this->assertIsString($result);
        $this->assertEquals(64, strlen($result));
    }

    /**
     * 测试格式化零
     */
    public function testFormatZero(): void
    {
        $result = IntegerFormatter::format(0);
        $this->assertIsString($result);
        $this->assertEquals(64, strlen($result));
        $this->assertStringEndsWith('0', $result);
    }

    /**
     * 测试格式化负数
     */
    public function testFormatNegative(): void
    {
        $result = IntegerFormatter::format(-10);
        $this->assertIsString($result);
        $this->assertEquals(64, strlen($result));
    }

    /**
     * 测试格式化大数
     */
    public function testFormatLargeNumber(): void
    {
        $result = IntegerFormatter::format('12345678901234567890');
        $this->assertIsString($result);
        $this->assertEquals(64, strlen($result));
    }

    /**
     * 测试格式化十六进制字符串
     */
    public function testFormatHexString(): void
    {
        $result = IntegerFormatter::format('0xff');
        $this->assertIsString($result);
        $this->assertEquals(64, strlen($result));
    }
}
