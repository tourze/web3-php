<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Formatters;

use phpseclib3\Math\BigInteger as BigNumber;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Formatters\BigNumberFormatter;

/**
 * @internal
 */
#[CoversClass(BigNumberFormatter::class)]
final class BigNumberFormatterTest extends TestCase
{
    public function testFormatWithInteger(): void
    {
        $result = BigNumberFormatter::format(123);

        $this->assertInstanceOf(BigNumber::class, $result);
        $this->assertSame('123', $result->toString());
    }

    public function testFormatWithString(): void
    {
        $result = BigNumberFormatter::format('456');

        $this->assertInstanceOf(BigNumber::class, $result);
        $this->assertSame('456', $result->toString());
    }

    public function testFormatWithFloatAsString(): void
    {
        $result = BigNumberFormatter::format('123.456');

        // Decimal numbers return array: [whole_part, fraction_part, precision, is_negative]
        $this->assertIsArray($result);
        $this->assertCount(4, $result);
        $this->assertInstanceOf(BigNumber::class, $result[0]); // whole part
        $this->assertInstanceOf(BigNumber::class, $result[1]); // fraction part
        $this->assertSame('123', $result[0]->toString());
        $this->assertSame('456', $result[1]->toString());
        $this->assertSame(3, $result[2]); // precision
        $this->assertFalse($result[3]); // not negative
    }

    public function testFormatWithBigNumber(): void
    {
        $bigNumber = new BigNumber('999999999999999999999999999999');
        $result = BigNumberFormatter::format($bigNumber);

        $this->assertInstanceOf(BigNumber::class, $result);
        $this->assertSame('999999999999999999999999999999', $result->toString());
    }

    public function testFormatWithZero(): void
    {
        $result = BigNumberFormatter::format(0);

        $this->assertInstanceOf(BigNumber::class, $result);
        $this->assertSame('0', $result->toString());
    }

    public function testFormatWithNegativeNumber(): void
    {
        $result = BigNumberFormatter::format(-123);

        $this->assertInstanceOf(BigNumber::class, $result);
        $this->assertSame('-123', $result->toString());
    }

    public function testFormatWithLargeStringNumber(): void
    {
        $largeNumber = '123456789012345678901234567890';
        $result = BigNumberFormatter::format($largeNumber);

        $this->assertInstanceOf(BigNumber::class, $result);
        $this->assertSame($largeNumber, $result->toString());
    }

    public function testFormatWithHexString(): void
    {
        $result = BigNumberFormatter::format('0xff');

        // Hex string is treated as hexadecimal number
        $this->assertInstanceOf(BigNumber::class, $result);
        $this->assertSame('255', $result->toString()); // 0xff = 255
    }

    public function testFormatWithBoolean(): void
    {
        $trueResult = BigNumberFormatter::format(true);
        $falseResult = BigNumberFormatter::format(false);

        $this->assertInstanceOf(BigNumber::class, $trueResult);
        $this->assertInstanceOf(BigNumber::class, $falseResult);
        $this->assertSame('1', $trueResult->toString());
        $this->assertSame('0', $falseResult->toString());
    }

    public function testFormatConsistency(): void
    {
        $value = '123456789';
        $result1 = BigNumberFormatter::format($value);
        $result2 = BigNumberFormatter::format($value);

        // Both results should be BigNumber instances for integer strings
        $this->assertInstanceOf(BigNumber::class, $result1);
        $this->assertInstanceOf(BigNumber::class, $result2);
        $this->assertSame($result1->toString(), $result2->toString());
    }

    public function testFormatWithVeryLargeNumber(): void
    {
        $veryLarge = '115792089237316195423570985008687907853269984665640564039457584007913129639935';
        $result = BigNumberFormatter::format($veryLarge);

        $this->assertInstanceOf(BigNumber::class, $result);
        $this->assertSame($veryLarge, $result->toString());
    }

    public function testFormatWithNegativeDecimal(): void
    {
        $result = BigNumberFormatter::format('-123.45');

        // Negative decimal returns array with negative flag
        $this->assertIsArray($result);
        $this->assertCount(4, $result);
        $this->assertSame('123', $result[0]->toString());
        $this->assertSame('45', $result[1]->toString());
        $this->assertSame(2, $result[2]); // precision
        $this->assertInstanceOf(BigNumber::class, $result[3]); // negative flag as BigNumber(-1)
        $this->assertSame('-1', $result[3]->toString());
    }
}
