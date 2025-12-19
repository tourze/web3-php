<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests;

use phpseclib3\Math\BigInteger as BigNumber;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Exception\InvalidArgumentException;
use Tourze\Web3PHP\Utils;

/**
 * @internal
 */
#[CoversClass(Utils::class)]
final class UtilsTest extends TestCase
{
    public function testSha3NullHash(): void
    {
        $this->assertSame('c5d2460186f7233c927e7db2dcc703c0e500b653ca82273b7bfad8045d85a470', Utils::SHA3_NULL_HASH);
    }

    public function testUnitsConstant(): void
    {
        $this->assertIsArray(Utils::UNITS);
        $this->assertArrayHasKey('wei', Utils::UNITS);
        $this->assertArrayHasKey('ether', Utils::UNITS);
        $this->assertSame('1', Utils::UNITS['wei']);
        $this->assertSame('1000000000000000000', Utils::UNITS['ether']);
    }

    public function testToHexWithInteger(): void
    {
        $this->assertSame('a', Utils::toHex(10));
        $this->assertSame('0xa', Utils::toHex(10, true));
        $this->assertSame('64', Utils::toHex(100));
        $this->assertSame('0', Utils::toHex(0));
    }

    public function testToHexWithString(): void
    {
        $this->assertSame('48656c6c6f', Utils::toHex('Hello'));
        $this->assertSame('0x48656c6c6f', Utils::toHex('Hello', true));
        $this->assertSame('', Utils::toHex(''));
    }

    public function testToHexWithBigNumber(): void
    {
        $bigNumber = new BigNumber('1000000000000000000');
        $result = Utils::toHex($bigNumber);
        $this->assertIsString($result);
        $this->assertSame('de0b6b3a7640000', $result);
    }

    public function testToHexThrowsExceptionForInvalidType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The value to toHex function is not support.');
        Utils::toHex((object) []);
    }

    public function testHexToBin(): void
    {
        $this->assertSame('Hello', Utils::hexToBin('48656c6c6f'));
        $this->assertSame('Hello', Utils::hexToBin('0x48656c6c6f'));
        $this->assertSame('', Utils::hexToBin(''));
    }

    public function testHexToBinThrowsExceptionForNonString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The value to hexToBin function must be string.');
        Utils::hexToBin(123);
    }

    public function testIsZeroPrefixed(): void
    {
        $this->assertTrue(Utils::isZeroPrefixed('0x1234'));
        $this->assertTrue(Utils::isZeroPrefixed('0X1234'));
        $this->assertFalse(Utils::isZeroPrefixed('1234'));
        $this->assertFalse(Utils::isZeroPrefixed(''));
    }

    public function testIsZeroPrefixedThrowsExceptionForNonString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The value to isZeroPrefixed function must be string.');
        Utils::isZeroPrefixed(123);
    }

    public function testStripZero(): void
    {
        $this->assertSame('1234', Utils::stripZero('0x1234'));
        $this->assertSame('1234', Utils::stripZero('0X1234'));
        $this->assertSame('1234', Utils::stripZero('1234'));
        $this->assertSame('', Utils::stripZero('0x'));
    }

    public function testIsNegative(): void
    {
        $this->assertTrue(Utils::isNegative('-123'));
        $this->assertFalse(Utils::isNegative('123'));
        $this->assertFalse(Utils::isNegative('0'));
        $this->assertFalse(Utils::isNegative(''));
    }

    public function testIsNegativeThrowsExceptionForNonString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The value to isNegative function must be string.');
        Utils::isNegative(-123);
    }

    public function testIsAddress(): void
    {
        // Valid addresses
        $this->assertTrue(Utils::isAddress('0x5aAeb6053F3E94C9b9A09f33669435E7Ef1BeAed'));
        $this->assertTrue(Utils::isAddress('0x5aaeb6053f3e94c9b9a09f33669435e7ef1beaed'));
        $this->assertTrue(Utils::isAddress('0xFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF'));
        $this->assertTrue(Utils::isAddress('5aAeb6053F3E94C9b9A09f33669435E7Ef1BeAed'));

        // Invalid addresses
        $this->assertFalse(Utils::isAddress('0x5aAeb6053F3E94C9b9A09f33669435E7Ef1BeAe'));
        $this->assertFalse(Utils::isAddress('0x5aAeb6053F3E94C9b9A09f33669435E7Ef1BeAedg'));
        $this->assertFalse(Utils::isAddress(''));
        $this->assertFalse(Utils::isAddress('invalid'));
    }

    public function testIsAddressThrowsExceptionForNonString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The value to isAddress function must be string.');
        Utils::isAddress(123);
    }

    public function testIsAddressChecksum(): void
    {
        // Valid checksum address
        $this->assertTrue(Utils::isAddressChecksum('0x5aAeb6053F3E94C9b9A09f33669435E7Ef1BeAed'));

        // Invalid checksum (all lowercase should fail checksum test)
        $this->assertFalse(Utils::isAddressChecksum('0x5aaeb6053f3e94c9b9a09f33669435e7ef1beaed'));
    }

    public function testIsAddressChecksumThrowsExceptionForNonString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The value to isAddressChecksum function must be string.');
        Utils::isAddressChecksum(123);
    }

    public function testToChecksumAddress(): void
    {
        $address = '0x5aaeb6053f3e94c9b9a09f33669435e7ef1beaed';
        $checksumAddress = Utils::toChecksumAddress($address);
        $this->assertSame('0x5aAeb6053F3E94C9b9A09f33669435E7Ef1BeAed', $checksumAddress);
        $this->assertTrue(Utils::isAddressChecksum($checksumAddress));
    }

    public function testToChecksumAddressThrowsExceptionForNonString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The value to toChecksumAddress function must be string.');
        Utils::toChecksumAddress(123);
    }

    public function testIsHex(): void
    {
        $this->assertTrue(Utils::isHex('0x1234abcd'));
        $this->assertTrue(Utils::isHex('1234abcd'));
        $this->assertTrue(Utils::isHex('0x'));
        $this->assertTrue(Utils::isHex(''));
        $this->assertFalse(Utils::isHex('0x1234abcg'));
        $this->assertFalse(Utils::isHex('123g'));
    }

    public function testSha3(): void
    {
        $result = Utils::sha3('Hello World');
        if (null === $result) {
            self::fail('sha3 should not return null for valid input');
        }
        $this->assertStringStartsWith('0x', $result);
        $this->assertSame(66, strlen($result)); // 0x + 64 hex characters

        // Test with hex input
        $result2 = Utils::sha3('0x48656c6c6f');
        if (null === $result2) {
            self::fail('sha3 should not return null for valid hex input');
        }
        $this->assertStringStartsWith('0x', $result2);

        // Test empty string
        $result3 = Utils::sha3('');
        $this->assertNull($result3); // Should return null for empty hash
    }

    public function testSha3ThrowsExceptionForNonString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The value to sha3 function must be string.');
        Utils::sha3(123);
    }

    public function testToString(): void
    {
        $this->assertSame('123', Utils::toString(123));
        $this->assertSame('123.45', Utils::toString(123.45));
        $this->assertSame('1', Utils::toString(true));
        $this->assertSame('', Utils::toString(false));
        $this->assertSame('hello', Utils::toString('hello'));
    }

    public function testToWeiWithString(): void
    {
        $wei = Utils::toWei('1', 'ether');
        $this->assertInstanceOf(BigNumber::class, $wei);
        $this->assertSame('1000000000000000000', $wei->toString());

        $wei2 = Utils::toWei('1000', 'wei');
        $this->assertSame('1000', $wei2->toString());

        $wei3 = Utils::toWei('1', 'gwei');
        $this->assertSame('1000000000', $wei3->toString());
    }

    public function testToWeiWithBigNumber(): void
    {
        $bigNumber = new BigNumber('1');
        $wei = Utils::toWei($bigNumber, 'ether');
        $this->assertInstanceOf(BigNumber::class, $wei);
        $this->assertSame('1000000000000000000', $wei->toString());
    }

    public function testToWeiWithFractionString(): void
    {
        $wei = Utils::toWei('0.5', 'ether');
        $this->assertInstanceOf(BigNumber::class, $wei);
        $this->assertSame('500000000000000000', $wei->toString());
    }

    public function testToWeiThrowsExceptionForInvalidNumber(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('toWei number must be string or bignumber.');
        Utils::toWei(123.45, 'ether');
    }

    public function testToWeiThrowsExceptionForInvalidUnit(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('toWei unit must be string.');
        Utils::toWei('1', 123);
    }

    public function testToWeiThrowsExceptionForUnsupportedUnit(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('toWei doesn\'t support invalid unit.');
        Utils::toWei('1', 'invalid');
    }

    public function testToEther(): void
    {
        $result = Utils::toEther('1000000000000000000', 'wei');
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertSame('1', $result[0]->toString());
        $this->assertSame('0', $result[1]->toString());
    }

    public function testFromWei(): void
    {
        $result = Utils::fromWei('1000000000000000000', 'ether');
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertSame('1', $result[0]->toString());
        $this->assertSame('0', $result[1]->toString());

        $result2 = Utils::fromWei('1000', 'wei');
        $this->assertSame('1000', $result2[0]->toString());
    }

    public function testFromWeiWithBigNumber(): void
    {
        $bigNumber = new BigNumber('1000000000000000000');
        $result = Utils::fromWei($bigNumber, 'ether');
        $this->assertIsArray($result);
        $this->assertSame('1', $result[0]->toString());
    }

    public function testFromWeiThrowsExceptionForInvalidUnit(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('fromWei unit must be string.');
        Utils::fromWei('1000', 123);
    }

    public function testFromWeiThrowsExceptionForUnsupportedUnit(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('fromWei doesn\'t support invalid unit.');
        Utils::fromWei('1000', 'invalid');
    }

    public function testJsonMethodToStringWithStdClass(): void
    {
        $json = new \stdClass();
        $json->name = 'transfer';
        $json->inputs = [
            (object) ['type' => 'address'],
            (object) ['type' => 'uint256'],
        ];

        $result = Utils::jsonMethodToString($json);
        $this->assertSame('transfer(address,uint256)', $result);
    }

    public function testJsonMethodToStringWithArray(): void
    {
        $json = [
            'name' => 'approve',
            'inputs' => [
                ['type' => 'address'],
                ['type' => 'uint256'],
            ],
        ];

        $result = Utils::jsonMethodToString($json);
        $this->assertSame('approve(address,uint256)', $result);
    }

    public function testJsonMethodToStringWithPreformattedName(): void
    {
        $json = [
            'name' => 'transfer(address,uint256)',
            'inputs' => [],
        ];

        $result = Utils::jsonMethodToString($json);
        $this->assertSame('transfer(address,uint256)', $result);
    }

    public function testJsonMethodToStringThrowsExceptionForInvalidType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('jsonMethodToString json must be array or stdClass.');
        Utils::jsonMethodToString('invalid');
    }

    public function testJsonToArrayWithStdClass(): void
    {
        $json = new \stdClass();
        $json->name = 'test';
        $json->nested = new \stdClass();
        $json->nested->value = 'nested_value';

        $result = Utils::jsonToArray($json);
        $this->assertIsArray($result);
        $this->assertSame('test', $result['name']);
        $this->assertIsArray($result['nested']);
        $this->assertSame('nested_value', $result['nested']['value']);
    }

    public function testJsonToArrayWithArray(): void
    {
        $json = [
            'name' => 'test',
            'nested' => (object) ['value' => 'nested_value'],
            'items' => [
                (object) ['item' => 'item1'],
            ],
        ];

        $result = Utils::jsonToArray($json);
        $this->assertIsArray($result);
        $this->assertSame('test', $result['name']);
        $this->assertIsArray($result['nested']);
        $this->assertSame('nested_value', $result['nested']['value']);
        $this->assertIsArray($result['items'][0]);
        $this->assertSame('item1', $result['items'][0]['item']);
    }

    public function testToBnWithBigNumber(): void
    {
        $bigNumber = new BigNumber('123456789');
        $result = Utils::toBn($bigNumber);
        $this->assertSame($bigNumber, $result);
    }

    public function testToBnWithInteger(): void
    {
        $result = Utils::toBn(123);
        $this->assertInstanceOf(BigNumber::class, $result);
        $this->assertSame('123', $result->toString());
    }

    public function testToBnWithNumericString(): void
    {
        $result = Utils::toBn('123456');
        $this->assertInstanceOf(BigNumber::class, $result);
        $this->assertSame('123456', $result->toString());
    }

    public function testToBnWithNegativeString(): void
    {
        $result = Utils::toBn('-123');
        $this->assertInstanceOf(BigNumber::class, $result);
        $this->assertSame('-123', $result->toString());
    }

    public function testToBnWithDecimalString(): void
    {
        $result = Utils::toBn('123.456');
        $this->assertIsArray($result);
        $this->assertCount(4, $result);
        $this->assertSame('123', $result[0]->toString());
        $this->assertSame('456', $result[1]->toString());
        $this->assertSame(3, $result[2]);
        $this->assertFalse($result[3]);
    }

    public function testToBnWithHexString(): void
    {
        $result = Utils::toBn('0xff');
        $this->assertInstanceOf(BigNumber::class, $result);
        $this->assertSame('255', $result->toString());

        $result2 = Utils::toBn('ff');
        if (is_array($result2)) {
            self::fail('toBn should not return array for simple hex string');
        }
        $this->assertSame('255', $result2->toString());
    }

    public function testToBnWithEmptyString(): void
    {
        $result = Utils::toBn('');
        $this->assertInstanceOf(BigNumber::class, $result);
        $this->assertSame('0', $result->toString());
    }

    public function testToBnThrowsExceptionForInvalidHex(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('toBn number must be valid hex string.');
        Utils::toBn('invalid');
    }

    public function testToBnThrowsExceptionForInvalidType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('toBn number must be BigNumber, string or int.');
        Utils::toBn([]);
    }

    public function testToBnThrowsExceptionForInvalidDecimal(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('toBn number must be a valid number.');
        Utils::toBn('123.456.789');
    }
}
