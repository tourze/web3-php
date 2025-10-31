<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Contracts\Types;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Contracts\Types\Uinteger;

/**
 * Uinteger 测试
 * @internal
 */
#[CoversClass(Uinteger::class)]
final class UintegerTest extends TestCase
{
    private Uinteger $uinteger;

    protected function setUp(): void
    {
        parent::setUp();

        $this->uinteger = new Uinteger();
    }

    /**
     * 测试是否是类型
     */
    public function testIsType(): void
    {
        $this->assertTrue($this->uinteger->isType('uint'));
        $this->assertTrue($this->uinteger->isType('uint256'));
        $this->assertTrue($this->uinteger->isType('uint[]'));
        $this->assertTrue($this->uinteger->isType('uint[2]'));
        $this->assertFalse($this->uinteger->isType('int256'));
        $this->assertFalse($this->uinteger->isType('string'));
    }

    /**
     * 测试是否是动态类型
     */
    public function testIsDynamicType(): void
    {
        $this->assertFalse($this->uinteger->isDynamicType());
    }

    /**
     * 测试输入格式化
     */
    public function testInputFormat(): void
    {
        $result = $this->uinteger->inputFormat(42, 'uint256');
        $this->assertIsString($result);
        // IntegerFormatter 可能返回不带前缀的十六进制字符串
        $this->assertMatchesRegularExpression('/^0x[a-f0-9]+$|^[a-f0-9]+$/', $result);
    }

    /**
     * 测试输出格式化
     */
    public function testOutputFormat(): void
    {
        $result = $this->uinteger->outputFormat('000000000000000000000000000000000000000000000000000000000000002a', 'uint256');
        $this->assertIsString($result);
        $this->assertEquals('42', $result);
    }

    /**
     * 测试带前缀的输出格式化
     */
    public function testOutputFormatWithPrefix(): void
    {
        $result = $this->uinteger->outputFormat('000000000000000000000000000000000000000000000000000000000000001a', 'uint256');
        $this->assertIsString($result);
        $this->assertEquals('26', $result);
    }
}
