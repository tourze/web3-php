<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Contracts\Types;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Contracts\Types\Boolean;

/**
 * @internal
 */
#[CoversClass(Boolean::class)]
final class BooleanTest extends TestCase
{
    private Boolean $boolean;

    public function testIsType(): void
    {
        $this->assertTrue($this->boolean->isType('bool'));
        $this->assertTrue($this->boolean->isType('bool[]'));
        $this->assertTrue($this->boolean->isType('bool[10]'));
        $this->assertFalse($this->boolean->isType('string'));
    }

    public function testIsDynamicType(): void
    {
        $this->assertFalse($this->boolean->isDynamicType());
    }

    public function testInputFormat(): void
    {
        $result = $this->boolean->inputFormat(true, 'bool');
        $this->assertIsString($result);
        $this->assertStringEndsWith('1', $result);

        $result = $this->boolean->inputFormat(false, 'bool');
        $this->assertIsString($result);
        $this->assertStringEndsWith('0', $result);
    }

    public function testOutputFormat(): void
    {
        $result = $this->boolean->outputFormat('0000000000000000000000000000000000000000000000000000000000000001', 'bool');
        $this->assertEquals('true', $result);

        $result = $this->boolean->outputFormat('0000000000000000000000000000000000000000000000000000000000000000', 'bool');
        $this->assertEquals('false', $result);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->boolean = new Boolean();
    }
}
