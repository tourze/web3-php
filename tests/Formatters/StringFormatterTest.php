<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Formatters;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Formatters\StringFormatter;

/**
 * StringFormatter 测试
 * @internal
 */
#[CoversClass(StringFormatter::class)]
final class StringFormatterTest extends TestCase
{
    /**
     * 测试格式化字符串
     */
    public function testFormatString(): void
    {
        $result = StringFormatter::format('hello');
        $this->assertIsString($result);
        $this->assertEquals('hello', $result);
    }

    /**
     * 测试格式化空字符串
     */
    public function testFormatEmptyString(): void
    {
        $result = StringFormatter::format('');
        $this->assertIsString($result);
        $this->assertEquals('', $result);
    }

    /**
     * 测试格式化中文
     */
    public function testFormatChinese(): void
    {
        $result = StringFormatter::format('你好');
        $this->assertIsString($result);
        $this->assertEquals('你好', $result);
    }

    /**
     * 测试格式化特殊字符
     */
    public function testFormatSpecialChars(): void
    {
        $result = StringFormatter::format('hello@world.com');
        $this->assertIsString($result);
        $this->assertEquals('hello@world.com', $result);
    }

    /**
     * 测试格式化数字字符串
     */
    public function testFormatNumberString(): void
    {
        $result = StringFormatter::format('12345');
        $this->assertIsString($result);
        $this->assertEquals('12345', $result);
    }

    /**
     * 测试格式化长字符串
     */
    public function testFormatLongString(): void
    {
        $result = StringFormatter::format('This is a very long string that should be properly formatted to hex');
        $this->assertIsString($result);
        $this->assertEquals('This is a very long string that should be properly formatted to hex', $result);
    }
}
