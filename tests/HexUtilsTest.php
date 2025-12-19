<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests;

use phpseclib3\Math\BigInteger as BigNumber;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Exception\InvalidArgumentException;
use Tourze\Web3PHP\HexUtils;

/**
 * @internal
 */
#[CoversClass(HexUtils::class)]
final class HexUtilsTest extends TestCase
{
    public function testToHexWithInteger(): void
    {
        $this->assertSame('a', HexUtils::toHex(10));
        $this->assertSame('0xa', HexUtils::toHex(10, true));
        $this->assertSame('64', HexUtils::toHex(100));
        $this->assertSame('0', HexUtils::toHex(0));
    }

    public function testToHexWithString(): void
    {
        $this->assertSame('48656c6c6f', HexUtils::toHex('Hello'));
        $this->assertSame('0x48656c6c6f', HexUtils::toHex('Hello', true));
        $this->assertSame('', HexUtils::toHex(''));
    }

    public function testToHexWithBigNumber(): void
    {
        $bigNumber = new BigNumber('1000000000000000000');
        $result = HexUtils::toHex($bigNumber);
        $this->assertIsString($result);
        $this->assertSame('de0b6b3a7640000', $result);
    }

    public function testToHexThrowsExceptionForInvalidType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The value to toHex function is not support.');
        HexUtils::toHex((object) []);
    }

    public function testHexToBin(): void
    {
        $this->assertSame('Hello', HexUtils::hexToBin('48656c6c6f'));
        $this->assertSame('Hello', HexUtils::hexToBin('0x48656c6c6f'));
        $this->assertSame('', HexUtils::hexToBin(''));
    }

    public function testHexToBinThrowsExceptionForNonString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The value to hexToBin function must be string.');
        HexUtils::hexToBin(123);
    }

    public function testIsZeroPrefixed(): void
    {
        $this->assertTrue(HexUtils::isZeroPrefixed('0x1234'));
        $this->assertTrue(HexUtils::isZeroPrefixed('0X1234'));
        $this->assertFalse(HexUtils::isZeroPrefixed('1234'));
        $this->assertFalse(HexUtils::isZeroPrefixed(''));
    }

    public function testIsZeroPrefixedThrowsExceptionForNonString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The value to isZeroPrefixed function must be string.');
        HexUtils::isZeroPrefixed(123);
    }

    public function testStripZero(): void
    {
        $this->assertSame('1234', HexUtils::stripZero('0x1234'));
        $this->assertSame('1234', HexUtils::stripZero('0X1234'));
        $this->assertSame('1234', HexUtils::stripZero('1234'));
        $this->assertSame('', HexUtils::stripZero('0x'));
    }

    public function testIsHex(): void
    {
        $this->assertTrue(HexUtils::isHex('0x1234abcd'));
        $this->assertTrue(HexUtils::isHex('1234abcd'));
        $this->assertTrue(HexUtils::isHex('0x'));
        $this->assertTrue(HexUtils::isHex(''));
        $this->assertFalse(HexUtils::isHex('0x1234abcg'));
        $this->assertFalse(HexUtils::isHex('123g'));
    }
}
