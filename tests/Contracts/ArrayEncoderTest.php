<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Contracts;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Contracts\ArrayEncoder;
use Tourze\Web3PHP\Contracts\Types\Uinteger;

/**
 * ArrayEncoder 测试
 *
 * @internal
 */
#[CoversClass(ArrayEncoder::class)]
final class ArrayEncoderTest extends TestCase
{
    private ArrayEncoder $arrayEncoder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->arrayEncoder = new ArrayEncoder();
    }

    public function testEncodeWithOffsetForDynamicArray(): void
    {
        $solidityType = new Uinteger();
        $encoded = ['0000000000000000000000000000000000000000000000000000000000000002', 'encoded1', 'encoded2'];

        $result = $this->arrayEncoder->encodeWithOffset('uint256[]', $solidityType, $encoded, 64);
        $this->assertIsString($result);
    }

    public function testEncodeWithOffsetForStaticArray(): void
    {
        $solidityType = new Uinteger();
        $encoded = ['encoded1', 'encoded2'];

        $result = $this->arrayEncoder->encodeWithOffset('uint256[2]', $solidityType, $encoded, 64);
        $this->assertIsString($result);
    }

    public function testEncodeWithOffsetForRegularType(): void
    {
        $solidityType = new Uinteger();
        $encoded = 'encoded_value';

        $result = $this->arrayEncoder->encodeWithOffset('uint256', $solidityType, $encoded, 64);
        $this->assertSame('encoded_value', $result);
    }
}
