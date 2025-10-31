<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Formatters;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Formatters\AddressFormatter;

/**
 * AddressFormatter 测试
 * @internal
 */
#[CoversClass(AddressFormatter::class)]
final class AddressFormatterTest extends TestCase
{
    /**
     * 测试格式化有效地址
     */
    public function testFormatValidAddress(): void
    {
        $address = '0x742d35Cc6634C0532925a3b844Bc9e7595f8568E';
        $result = AddressFormatter::format($address);
        $this->assertIsString($result);
        $this->assertStringStartsWith('0x', $result);
        $this->assertEquals(42, strlen($result));
    }

    /**
     * 测试格式化不带前缀的地址
     */
    public function testFormatAddressWithoutPrefix(): void
    {
        $address = '742d35Cc6634C0532925a3b844Bc9e7595f8568E';
        $result = AddressFormatter::format($address);
        $this->assertIsString($result);
        $this->assertStringStartsWith('0x', $result);
        $this->assertEquals(42, strlen($result));
    }

    /**
     * 测试格式化小写地址
     */
    public function testFormatLowercaseAddress(): void
    {
        $address = '0x742d35cc6634c0532925a3b844bc9e7595f8568e';
        $result = AddressFormatter::format($address);
        $this->assertIsString($result);
        $this->assertStringStartsWith('0x', $result);
        $this->assertEquals(42, strlen($result));
    }

    /**
     * 测试格式化数值
     */
    public function testFormatNumericValue(): void
    {
        $result = AddressFormatter::format('123456789');
        $this->assertIsString($result);
        $this->assertStringStartsWith('0x', $result);
        $this->assertEquals(42, strlen($result));
    }

    /**
     * 测试格式化空值
     */
    public function testFormatEmptyValue(): void
    {
        $result = AddressFormatter::format('');
        $this->assertIsString($result);
        $this->assertStringStartsWith('0x', $result);
    }
}
