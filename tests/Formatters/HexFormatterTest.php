<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Formatters;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Formatters\HexFormatter;

/**
 * HexFormatter 测试
 * @internal
 */
#[CoversClass(HexFormatter::class)]
final class HexFormatterTest extends TestCase
{
    /**
     * 测试格式化十六进制值
     */
    public function testFormat(): void
    {
        $result = HexFormatter::format('0x68656c6c6f');
        $this->assertIsString($result);
        $this->assertStringStartsWith('0x', $result);
    }

    /**
     * 测试格式化字符串
     */
    public function testFormatString(): void
    {
        $result = HexFormatter::format('hello');
        $this->assertIsString($result);
    }

    /**
     * 测试格式化数值
     */
    public function testFormatNumber(): void
    {
        $result = HexFormatter::format(42);
        $this->assertIsString($result);
    }
}
