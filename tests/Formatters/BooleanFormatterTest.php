<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Formatters;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Formatters\BooleanFormatter;

/**
 * @internal
 */
#[CoversClass(BooleanFormatter::class)]
final class BooleanFormatterTest extends TestCase
{
    public function testFormatWithBooleanTrue(): void
    {
        $result = BooleanFormatter::format(true);

        $this->assertSame('1', $result);
    }

    public function testFormatWithBooleanFalse(): void
    {
        $result = BooleanFormatter::format(false);

        $this->assertSame('0', $result);
    }

    public function testFormatWithInteger1(): void
    {
        $result = BooleanFormatter::format(1);

        $this->assertSame('1', $result);
    }

    public function testFormatWithInteger0(): void
    {
        $result = BooleanFormatter::format(0);

        $this->assertSame('0', $result);
    }

    public function testFormatWithNonZeroInteger(): void
    {
        $result = BooleanFormatter::format(42);

        $this->assertSame('1', $result);
    }

    public function testFormatWithNegativeInteger(): void
    {
        $result = BooleanFormatter::format(-1);

        $this->assertSame('1', $result);
    }

    public function testFormatWithNonEmptyString(): void
    {
        $result = BooleanFormatter::format('hello');

        $this->assertSame('1', $result);
    }

    public function testFormatWithEmptyString(): void
    {
        $result = BooleanFormatter::format('');

        $this->assertSame('0', $result);
    }

    public function testFormatWithStringZero(): void
    {
        $result = BooleanFormatter::format('0');

        $this->assertSame('0', $result);
    }

    public function testFormatWithStringOne(): void
    {
        $result = BooleanFormatter::format('1');

        $this->assertSame('1', $result);
    }

    public function testFormatWithFloat(): void
    {
        $result1 = BooleanFormatter::format(0.0);
        $result2 = BooleanFormatter::format(1.5);

        $this->assertSame('0', $result1);
        $this->assertSame('1', $result2);
    }

    public function testFormatWithNull(): void
    {
        $result = BooleanFormatter::format(null);

        $this->assertSame('0', $result);
    }

    public function testFormatWithArray(): void
    {
        $emptyArray = BooleanFormatter::format([]);
        $nonEmptyArray = BooleanFormatter::format([1, 2, 3]);

        $this->assertSame('0', $emptyArray);
        $this->assertSame('1', $nonEmptyArray);
    }

    public function testFormatConsistency(): void
    {
        $testCases = [
            [true, '1'],
            [false, '0'],
            [1, '1'],
            [0, '0'],
            ['', '0'],
            ['hello', '1'],
            [null, '0'],
        ];

        foreach ($testCases as [$input, $expected]) {
            $result1 = BooleanFormatter::format($input);
            $result2 = BooleanFormatter::format($input);

            $this->assertSame($expected, $result1);
            $this->assertSame($result1, $result2);
        }
    }

    public function testFormatReturnType(): void
    {
        $testValues = [true, false, 1, 0, 'test', '', null];

        foreach ($testValues as $value) {
            $result = BooleanFormatter::format($value);
            $this->assertIsString($result);
            $this->assertTrue('0' === $result || '1' === $result);
        }
    }
}
