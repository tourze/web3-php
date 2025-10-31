<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Formatters;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Formatters\NumberFormatter;

/**
 * NumberFormatter 测试
 * @internal
 */
#[CoversClass(NumberFormatter::class)]
final class NumberFormatterTest extends TestCase
{
    /**
     * 测试格式化整数
     */
    public function testFormatInteger(): void
    {
        $result = NumberFormatter::format(42);
        $this->assertIsInt($result);
        $this->assertEquals(42, $result);
    }

    /**
     * 测试格式化零
     */
    public function testFormatZero(): void
    {
        $result = NumberFormatter::format(0);
        $this->assertIsInt($result);
        $this->assertEquals(0, $result);
    }
}
