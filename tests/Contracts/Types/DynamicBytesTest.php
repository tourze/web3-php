<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Contracts\Types;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Contracts\Types\DynamicBytes;
use Tourze\Web3PHP\Exception\InvalidArgumentException;

/**
 * DynamicBytes 测试
 * @internal
 */
#[CoversClass(DynamicBytes::class)]
final class DynamicBytesTest extends TestCase
{
    private DynamicBytes $dynamicBytes;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dynamicBytes = new DynamicBytes();
    }

    /**
     * 测试是否是类型
     */
    public function testIsType(): void
    {
        $this->assertTrue($this->dynamicBytes->isType('bytes'));
        $this->assertTrue($this->dynamicBytes->isType('bytes[]'));
        $this->assertTrue($this->dynamicBytes->isType('bytes[2]'));
        $this->assertFalse($this->dynamicBytes->isType('uint256'));
        $this->assertFalse($this->dynamicBytes->isType('string'));
    }

    /**
     * 测试是否是动态类型
     */
    public function testIsDynamicType(): void
    {
        $this->assertTrue($this->dynamicBytes->isDynamicType());
    }

    /**
     * 测试输入格式化
     */
    public function testInputFormat(): void
    {
        $result = $this->dynamicBytes->inputFormat('0x68656c6c6f', 'bytes');
        $this->assertIsString($result);
        $this->assertStringStartsWith('0', $result);
    }

    /**
     * 测试无效输入格式化
     */
    public function testInputFormatWithInvalidHex(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('输入格式化的值必须是十六进制字节。');
        $this->dynamicBytes->inputFormat('invalid', 'bytes');
    }

    /**
     * 测试输出格式化
     */
    public function testOutputFormat(): void
    {
        $result = $this->dynamicBytes->outputFormat('000000000000000000000000000000000000000000000000000000000000000568656c6c6f', 'bytes');
        $this->assertIsString($result);
        $this->assertStringStartsWith('0x', $result);
    }

    /**
     * 测试零值输出格式化
     */
    public function testOutputFormatWithZero(): void
    {
        $result = $this->dynamicBytes->outputFormat(str_repeat('0', 64), 'bytes');
        $this->assertEquals('0', $result);
    }
}
