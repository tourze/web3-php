<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\AddressUtils;
use Tourze\Web3PHP\Exception\InvalidArgumentException;

/**
 * @internal
 */
#[CoversClass(AddressUtils::class)]
final class AddressUtilsTest extends TestCase
{
    public function testIsAddress(): void
    {
        // Valid addresses
        $this->assertTrue(AddressUtils::isAddress('0x5aAeb6053F3E94C9b9A09f33669435E7Ef1BeAed'));
        $this->assertTrue(AddressUtils::isAddress('0x5aaeb6053f3e94c9b9a09f33669435e7ef1beaed'));
        $this->assertTrue(AddressUtils::isAddress('0xFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF'));
        $this->assertTrue(AddressUtils::isAddress('5aAeb6053F3E94C9b9A09f33669435E7Ef1BeAed'));

        // Invalid addresses
        $this->assertFalse(AddressUtils::isAddress('0x5aAeb6053F3E94C9b9A09f33669435E7Ef1BeAe'));
        $this->assertFalse(AddressUtils::isAddress('0x5aAeb6053F3E94C9b9A09f33669435E7Ef1BeAedg'));
        $this->assertFalse(AddressUtils::isAddress(''));
        $this->assertFalse(AddressUtils::isAddress('invalid'));
    }

    public function testIsAddressThrowsExceptionForNonString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The value to isAddress function must be string.');
        AddressUtils::isAddress(123);
    }

    public function testIsAddressChecksum(): void
    {
        // Valid checksum address
        $this->assertTrue(AddressUtils::isAddressChecksum('0x5aAeb6053F3E94C9b9A09f33669435E7Ef1BeAed'));

        // Invalid checksum (all lowercase should fail checksum test)
        $this->assertFalse(AddressUtils::isAddressChecksum('0x5aaeb6053f3e94c9b9a09f33669435e7ef1beaed'));
    }

    public function testIsAddressChecksumThrowsExceptionForNonString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The value to isAddressChecksum function must be string.');
        AddressUtils::isAddressChecksum(123);
    }

    public function testToChecksumAddress(): void
    {
        $address = '0x5aaeb6053f3e94c9b9a09f33669435e7ef1beaed';
        $checksumAddress = AddressUtils::toChecksumAddress($address);
        $this->assertSame('0x5aAeb6053F3E94C9b9A09f33669435E7Ef1BeAed', $checksumAddress);
        $this->assertTrue(AddressUtils::isAddressChecksum($checksumAddress));
    }

    public function testToChecksumAddressThrowsExceptionForNonString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The value to toChecksumAddress function must be string.');
        AddressUtils::toChecksumAddress(123);
    }
}
