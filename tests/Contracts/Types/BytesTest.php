<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Contracts\Types;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Contracts\Types\Bytes;

/**
 * @internal
 */
#[CoversClass(Bytes::class)]
final class BytesTest extends TestCase
{
    private Bytes $bytes;

    public function testIsType(): void
    {
        $this->assertTrue($this->bytes->isType('bytes32'));
        $this->assertTrue($this->bytes->isType('bytes1'));
        $this->assertFalse($this->bytes->isType('bytes'));
        $this->assertFalse($this->bytes->isType('string'));
    }

    public function testIsDynamicType(): void
    {
        $this->assertFalse($this->bytes->isDynamicType());
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->bytes = new Bytes();
    }

    public function testInputFormat(): void
    {
        $value = '0x1234567890abcdef';
        $name = 'bytes32';
        $result = $this->bytes->inputFormat($value, $name);

        $this->assertIsString($result);
    }

    public function testOutputFormat(): void
    {
        $value = '0x1234567890abcdef';
        $name = 'bytes32';
        $result = $this->bytes->outputFormat($value, $name);

        $this->assertIsString($result);
    }
}
