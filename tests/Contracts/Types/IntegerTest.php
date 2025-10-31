<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Contracts\Types;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Contracts\Types\Integer;

/**
 * Integer 测试
 * @internal
 */
#[CoversClass(Integer::class)]
final class IntegerTest extends TestCase
{
    private Integer $integer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->integer = new Integer();
    }

    /**
     * 测试是否是类型
     */
    public function testIsType(): void
    {
        $this->assertTrue($this->integer->isType('int'));
        $this->assertTrue($this->integer->isType('int256'));
        $this->assertTrue($this->integer->isType('int[]'));
        $this->assertTrue($this->integer->isType('int[2]'));
        $this->assertFalse($this->integer->isType('uint256'));
        $this->assertFalse($this->integer->isType('string'));
    }

    /**
     * 测试是否是动态类型
     */
    public function testIsDynamicType(): void
    {
        $this->assertFalse($this->integer->isDynamicType());
    }

    /**
     * 测试输入格式化
     */
    public function testInputFormat(): void
    {
        $result = $this->integer->inputFormat(42, 'int256');
        $this->assertIsString($result);
        $this->assertStringStartsWith('0x', $result);
    }

    /**
     * 测试输出格式化
     */
    public function testOutputFormat(): void
    {
        $result = $this->integer->outputFormat('000000000000000000000000000000000000000000000000000000000000002a', 'int256');
        $this->assertIsString($result);
        $this->assertEquals('42', $result);
    }

    /**
     * 测试带前缀的输出格式化
     */
    public function testOutputFormatWithPrefix(): void
    {
        $result = $this->integer->outputFormat('000000000000000000000000000000000000000000000000000000000000001a', 'int256');
        $this->assertIsString($result);
        $this->assertEquals('26', $result);
    }
}
