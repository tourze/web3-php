<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Contracts\Types;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Contracts\Types\Str;

/**
 * Str 测试
 * @internal
 */
#[CoversClass(Str::class)]
final class StrTest extends TestCase
{
    private Str $str;

    protected function setUp(): void
    {
        parent::setUp();

        $this->str = new Str();
    }

    /**
     * 测试是否是类型
     */
    public function testIsType(): void
    {
        $this->assertTrue($this->str->isType('string'));
        $this->assertTrue($this->str->isType('string[]'));
        $this->assertTrue($this->str->isType('string[2]'));
        $this->assertFalse($this->str->isType('bytes'));
        $this->assertFalse($this->str->isType('int256'));
    }

    /**
     * 测试是否是动态类型
     */
    public function testIsDynamicType(): void
    {
        $this->assertTrue($this->str->isDynamicType());
    }

    /**
     * 测试输入格式化
     */
    public function testInputFormat(): void
    {
        $result = $this->str->inputFormat('hello', 'string');
        $this->assertIsString($result);
        $this->assertStringStartsWith('0x', $result);
    }

    /**
     * 测试空字符串输入格式化
     */
    public function testInputFormatWithEmptyString(): void
    {
        $result = $this->str->inputFormat('', 'string');
        $this->assertIsString($result);
        $this->assertStringStartsWith('0x', $result);
    }

    /**
     * 测试输出格式化
     */
    public function testOutputFormat(): void
    {
        $hexValue = '000000000000000000000000000000000000000000000000000000000000000568656c6c6f';
        $result = $this->str->outputFormat($hexValue, 'string');
        $this->assertIsString($result);
        $this->assertEquals('hello', $result);
    }

    /**
     * 测试空字符串输出格式化
     */
    public function testOutputFormatWithEmptyString(): void
    {
        $hexValue = '0000000000000000000000000000000000000000000000000000000000000000';
        $result = $this->str->outputFormat($hexValue, 'string');
        $this->assertIsString($result);
        $this->assertEquals('', $result);
    }
}
