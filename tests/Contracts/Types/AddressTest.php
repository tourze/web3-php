<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Contracts\Types;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Contracts\Types\Address;

/**
 * @internal
 */
#[CoversClass(Address::class)]
final class AddressTest extends TestCase
{
    private Address $address;

    public function testIsType(): void
    {
        $this->assertTrue($this->address->isType('address'));
        $this->assertTrue($this->address->isType('address[]'));
        $this->assertTrue($this->address->isType('address[10]'));
        $this->assertFalse($this->address->isType('string'));
        $this->assertFalse($this->address->isType('uint256'));
    }

    public function testIsDynamicType(): void
    {
        $this->assertFalse($this->address->isDynamicType());
    }

    public function testInputFormat(): void
    {
        $result = $this->address->inputFormat('0x1234567890abcdef1234567890abcdef12345678', 'address');
        $this->assertIsString($result);
    }

    public function testOutputFormat(): void
    {
        $result = $this->address->outputFormat('0000000000000000000000001234567890abcdef1234567890abcdef12345678', 'address');
        $this->assertIsString($result);
        $this->assertStringStartsWith('0x', $result);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->address = new Address();
    }
}
