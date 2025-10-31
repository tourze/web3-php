<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Contracts;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Contracts\SolidityType;
use Tourze\Web3PHP\Exception\InvalidArgumentException;

/**
 * @internal
 */
#[CoversClass(SolidityType::class)]
final class SolidityTypeTest extends TestCase
{
    private SolidityType $solidityType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->solidityType = new TestSolidityType();
    }

    public function testMagicGetMethod(): void
    {
        // Test with non-existent property
        // @phpstan-ignore-next-line 故意访问未定义属性测试魔术方法
        $result = $this->solidityType->nonExistentProperty;
        $this->assertFalse($result);
    }

    public function testMagicSetMethod(): void
    {
        // Test with non-existent method
        // @phpstan-ignore-next-line 故意设置未定义属性测试魔术方法
        $this->solidityType->nonExistentProperty = 'value';
        // Since __set returns void, we just verify no exception is thrown
        // 测试设置属性后对象仍然存在
        $this->assertInstanceOf(SolidityType::class, $this->solidityType);
    }

    public function testNestedTypesWithArrayType(): void
    {
        $result = $this->solidityType->nestedTypes('uint256[]');
        $this->assertIsArray($result);
        $this->assertSame(['[]'], $result);

        $result2 = $this->solidityType->nestedTypes('uint256[10]');
        $this->assertIsArray($result2);
        $this->assertSame(['[10]'], $result2);

        $result3 = $this->solidityType->nestedTypes('uint256[][5]');
        $this->assertIsArray($result3);
        $this->assertSame(['[]', '[5]'], $result3);
    }

    public function testNestedTypesWithNonArrayType(): void
    {
        $result = $this->solidityType->nestedTypes('uint256');
        $this->assertFalse($result);

        $result2 = $this->solidityType->nestedTypes('address');
        $this->assertFalse($result2);
    }

    public function testNestedTypesThrowsExceptionForNonString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('nestedTypes name must string.');
        // @phpstan-ignore-next-line
        $this->solidityType->nestedTypes(123);
    }

    public function testNestedNameWithArrayType(): void
    {
        $result = $this->solidityType->nestedName('uint256[]');
        $this->assertSame('uint256', $result);

        $result2 = $this->solidityType->nestedName('uint256[10]');
        $this->assertSame('uint256', $result2);

        $result3 = $this->solidityType->nestedName('uint256[][5]');
        $this->assertSame('uint256[]', $result3);
    }

    public function testNestedNameWithNonArrayType(): void
    {
        $result = $this->solidityType->nestedName('uint256');
        $this->assertSame('uint256', $result);

        $result2 = $this->solidityType->nestedName('address');
        $this->assertSame('address', $result2);
    }

    public function testNestedNameThrowsExceptionForNonString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('nestedName name must string.');
        // @phpstan-ignore-next-line
        $this->solidityType->nestedName(123);
    }

    public function testIsDynamicArrayWithDynamicArray(): void
    {
        $this->assertTrue($this->solidityType->isDynamicArray('uint256[]'));
        $this->assertTrue($this->solidityType->isDynamicArray('address[]'));
        $this->assertTrue($this->solidityType->isDynamicArray('uint256[][10]'));
    }

    public function testIsDynamicArrayWithStaticArray(): void
    {
        $this->assertFalse($this->solidityType->isDynamicArray('uint256[10]'));
        $this->assertFalse($this->solidityType->isDynamicArray('address[5]'));
    }

    public function testIsDynamicArrayWithNonArrayType(): void
    {
        $this->assertFalse($this->solidityType->isDynamicArray('uint256'));
        $this->assertFalse($this->solidityType->isDynamicArray('address'));
    }

    public function testIsStaticArrayWithStaticArray(): void
    {
        $this->assertTrue($this->solidityType->isStaticArray('uint256[10]'));
        $this->assertTrue($this->solidityType->isStaticArray('address[5]'));
        $this->assertTrue($this->solidityType->isStaticArray('uint256[1]'));
    }

    public function testIsStaticArrayWithDynamicArray(): void
    {
        $this->assertFalse($this->solidityType->isStaticArray('uint256[]'));
        $this->assertFalse($this->solidityType->isStaticArray('address[]'));
    }

    public function testIsStaticArrayWithNonArrayType(): void
    {
        $this->assertFalse($this->solidityType->isStaticArray('uint256'));
        $this->assertFalse($this->solidityType->isStaticArray('address'));
    }

    public function testStaticArrayLengthWithStaticArray(): void
    {
        $this->assertSame(10, $this->solidityType->staticArrayLength('uint256[10]'));
        $this->assertSame(5, $this->solidityType->staticArrayLength('address[5]'));
        $this->assertSame(1, $this->solidityType->staticArrayLength('uint256[1]'));
        $this->assertSame(100, $this->solidityType->staticArrayLength('uint256[100]'));
    }

    public function testStaticArrayLengthWithDynamicArray(): void
    {
        $this->assertSame(1, $this->solidityType->staticArrayLength('uint256[]'));
        $this->assertSame(1, $this->solidityType->staticArrayLength('address[]'));
    }

    public function testStaticArrayLengthWithNonArrayType(): void
    {
        $this->assertSame(1, $this->solidityType->staticArrayLength('uint256'));
        $this->assertSame(1, $this->solidityType->staticArrayLength('address'));
    }

    public function testStaticPartLengthWithNonArrayType(): void
    {
        $this->assertSame(32, $this->solidityType->staticPartLength('uint256'));
        $this->assertSame(32, $this->solidityType->staticPartLength('address'));
    }

    public function testStaticPartLengthWithStaticArray(): void
    {
        $this->assertSame(320, $this->solidityType->staticPartLength('uint256[10]')); // 32 * 10
        $this->assertSame(160, $this->solidityType->staticPartLength('address[5]')); // 32 * 5
        $this->assertSame(32, $this->solidityType->staticPartLength('uint256[1]')); // 32 * 1
    }

    public function testStaticPartLengthWithDynamicArray(): void
    {
        $this->assertSame(32, $this->solidityType->staticPartLength('uint256[]')); // 32 * 1
        $this->assertSame(32, $this->solidityType->staticPartLength('address[]')); // 32 * 1
    }

    public function testStaticPartLengthWithMultiDimensionalArray(): void
    {
        $this->assertSame(64, $this->solidityType->staticPartLength('uint256[2][1]')); // 32 * 2 * 1
    }

    public function testIsDynamicType(): void
    {
        // Base implementation always returns false
        $this->assertFalse($this->solidityType->isDynamicType());
    }

    public function testEncodeWithDynamicArray(): void
    {
        // Create anonymous class to replace mock
        $mockType = new class extends SolidityType {
            private int $inputFormatCallCount = 0;

            public function inputFormat(mixed $value, mixed $name = ''): string
            {
                ++$this->inputFormatCallCount;

                return 'encoded_value';
            }
        };

        $values = ['value1', 'value2', 'value3'];
        $result = $mockType->encode($values, 'uint256[]');

        $this->assertIsArray($result);
        $this->assertCount(4, $result); // Length + 3 values
        $this->assertSame('0000000000000000000000000000000000000000000000000000000000000003', $result[0]); // Length = 3
    }

    public function testEncodeWithStaticArray(): void
    {
        // Create anonymous class to replace mock
        $mockType = new class extends SolidityType {
            private int $inputFormatCallCount = 0;

            public function inputFormat(mixed $value, mixed $name = ''): string
            {
                ++$this->inputFormatCallCount;

                return 'encoded_value';
            }
        };

        $values = ['value1', 'value2'];
        $result = $mockType->encode($values, 'uint256[2]');

        $this->assertIsArray($result);
        $this->assertCount(2, $result); // Just the values, no length prefix
    }

    public function testEncodeWithRegularType(): void
    {
        // Create anonymous class to replace mock
        $mockType = new class extends SolidityType {
            public function inputFormat(mixed $value, mixed $name = ''): string
            {
                return 'encoded_value';
            }
        };

        $result = $mockType->encode('value', 'uint256');
        $this->assertSame('encoded_value', $result);
    }

    public function testDecodeWithDynamicArray(): void
    {
        // Create anonymous class to replace mock
        $mockType = new class extends SolidityType {
            private int $outputFormatCallCount = 0;

            public function outputFormat(mixed $value, mixed $name = ''): string
            {
                ++$this->outputFormatCallCount;

                return 'decoded_value';
            }
        };

        // Mock data for dynamic array:
        // - First 32 bytes (64 chars): pointer to array data = 0x20 (32 decimal)
        // - Next 32 bytes: array length = 2
        // - Next 32 bytes: first element value = 1
        // - Next 32 bytes: second element value = 2
        $data = '0000000000000000000000000000000000000000000000000000000000000020' . // pointer to 32
                '0000000000000000000000000000000000000000000000000000000000000002' . // length = 2
                '0000000000000000000000000000000000000000000000000000000000000001' . // value 1
                '0000000000000000000000000000000000000000000000000000000000000002';  // value 2

        $result = $mockType->decode($data, 0, 'uint256[]');
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
    }

    public function testDecodeWithStaticArray(): void
    {
        // Create anonymous class to replace mock
        $mockType = new class extends SolidityType {
            private int $outputFormatCallCount = 0;

            public function outputFormat(mixed $value, mixed $name = ''): string
            {
                ++$this->outputFormatCallCount;

                return 'decoded_value';
            }
        };

        // Mock data: two values
        $data = '0000000000000000000000000000000000000000000000000000000000000001' . // value 1
                '0000000000000000000000000000000000000000000000000000000000000002';  // value 2

        $result = $mockType->decode($data, 0, 'uint256[2]');
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
    }

    public function testDecodeWithDynamicType(): void
    {
        // Create anonymous class that simulates a dynamic type
        $mockType = new class extends SolidityType {
            private int $isDynamicTypeCallCount = 0;

            private int $outputFormatCallCount = 0;

            public function isDynamicType(): bool
            {
                ++$this->isDynamicTypeCallCount;

                return true;
            }

            public function outputFormat(mixed $value, mixed $name = ''): string
            {
                ++$this->outputFormatCallCount;

                return 'decoded_value';
            }
        };

        // Mock data: offset pointer at start, then length and data
        $data = '0000000000000000000000000000000000000000000000000000000000000020' . // offset = 32
                '0000000000000000000000000000000000000000000000000000000000000005' . // length = 5
                '48656c6c6f000000000000000000000000000000000000000000000000000000';  // "Hello" padded

        $result = $mockType->decode($data, 0, 'string');
        $this->assertSame('decoded_value', $result);
    }

    public function testDecodeWithRegularType(): void
    {
        // Create anonymous class to replace mock
        $mockType = new class extends SolidityType {
            public function outputFormat(mixed $value, mixed $name = ''): string
            {
                return 'decoded_value';
            }
        };

        $data = '0000000000000000000000000000000000000000000000000000000000000001'; // value = 1

        $result = $mockType->decode($data, 0, 'uint256');
        $this->assertSame('decoded_value', $result);
    }

    public function testInputFormat(): void
    {
        // Create anonymous class to replace mock
        $mockType = new class extends SolidityType {
            public function inputFormat(mixed $value, mixed $name = ''): string
            {
                return (string) $value;
            }
        };

        $value = 'test_value';
        $name = 'uint256';
        $result = $mockType->inputFormat($value, $name);

        $this->assertIsString($result);
    }

    public function testOutputFormat(): void
    {
        // Create anonymous class to replace mock
        $mockType = new class extends SolidityType {
            public function outputFormat(mixed $value, mixed $name = ''): string
            {
                return (string) $value;
            }
        };

        $value = 'test_value';
        $name = 'uint256';
        $result = $mockType->outputFormat($value, $name);

        $this->assertIsString($result);
    }
}
