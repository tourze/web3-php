<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Contracts;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Contracts\Ethabi;
use Tourze\Web3PHP\Contracts\Types\Address;
use Tourze\Web3PHP\Contracts\Types\Boolean;
use Tourze\Web3PHP\Contracts\Types\Bytes;
use Tourze\Web3PHP\Contracts\Types\DynamicBytes;
use Tourze\Web3PHP\Contracts\Types\Integer;
use Tourze\Web3PHP\Contracts\Types\Str;
use Tourze\Web3PHP\Contracts\Types\Uinteger;
use Tourze\Web3PHP\Exception\InvalidArgumentException;

/**
 * @internal
 */
#[CoversClass(Ethabi::class)]
final class EthabiTest extends TestCase
{
    private Ethabi $ethabi;

    protected function setUp(): void
    {
        parent::setUp();

        $types = [
            'address' => Address::class,
            'bool' => Boolean::class,
            'bytes' => Bytes::class,
            'dynamicBytes' => DynamicBytes::class,
            'int' => Integer::class,
            'string' => Str::class,
            'uint' => Uinteger::class,
        ];
        $this->ethabi = new Ethabi($types);
    }

    public function testConstructor(): void
    {
        $ethabi = new Ethabi();
        $this->assertInstanceOf(Ethabi::class, $ethabi);

        $types = ['address' => Address::class];
        $ethabi2 = new Ethabi($types);
        $this->assertInstanceOf(Ethabi::class, $ethabi2);
    }

    public function testConstructorWithNonArrayTypes(): void
    {
        $ethabi = new Ethabi([]);
        $this->assertInstanceOf(Ethabi::class, $ethabi);
    }

    public function testMagicGetMethod(): void
    {
        // Test with non-existent property
        $result = $this->ethabi->nonExistentProperty; // @phpstan-ignore property.notFound
        $this->assertFalse($result);
    }

    public function testMagicSetMethod(): void
    {
        // Test with non-existent method
        $this->ethabi->nonExistentProperty = 'value'; // @phpstan-ignore property.notFound
        // Since __set returns void, we just verify no exception is thrown
        // 测试设置属性后对象仍然存在
        $this->assertInstanceOf(Ethabi::class, $this->ethabi);
    }

    public function testCallStaticMethod(): void
    {
        // Test that __callStatic throws exception for non-existent method
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Method not found: nonExistentMethod');
        Ethabi::nonExistentMethod(); // @phpstan-ignore staticMethod.notFound
    }

    public function testEncodeFunctionSignatureWithString(): void
    {
        $signature = $this->ethabi->encodeFunctionSignature('transfer(address,uint256)');
        $this->assertIsString($signature);
        $this->assertSame(10, strlen($signature)); // 0x + 8 hex characters
        $this->assertStringStartsWith('0x', $signature);
    }

    public function testEncodeFunctionSignatureWithStdClass(): void
    {
        $function = new \stdClass();
        $function->name = 'transfer';
        $function->inputs = [
            (object) ['type' => 'address'],
            (object) ['type' => 'uint256'],
        ];

        $signature = $this->ethabi->encodeFunctionSignature($function);
        $this->assertIsString($signature);
        $this->assertSame(10, strlen($signature));
        $this->assertStringStartsWith('0x', $signature);
    }

    public function testEncodeFunctionSignatureWithArray(): void
    {
        $function = [
            'name' => 'approve',
            'inputs' => [
                ['type' => 'address'],
                ['type' => 'uint256'],
            ],
        ];

        $signature = $this->ethabi->encodeFunctionSignature($function);
        $this->assertIsString($signature);
        $this->assertSame(10, strlen($signature));
        $this->assertStringStartsWith('0x', $signature);
    }

    public function testEncodeEventSignatureWithString(): void
    {
        $signature = $this->ethabi->encodeEventSignature('Transfer(address,address,uint256)');
        $this->assertIsString($signature);
        $this->assertSame(66, strlen($signature)); // 0x + 64 hex characters
        $this->assertStringStartsWith('0x', $signature);
    }

    public function testEncodeEventSignatureWithStdClass(): void
    {
        $event = new \stdClass();
        $event->name = 'Transfer';
        $event->inputs = [
            (object) ['type' => 'address'],
            (object) ['type' => 'address'],
            (object) ['type' => 'uint256'],
        ];

        $signature = $this->ethabi->encodeEventSignature($event);
        $this->assertIsString($signature);
        $this->assertSame(66, strlen($signature));
        $this->assertStringStartsWith('0x', $signature);
    }

    public function testEncodeParameterThrowsExceptionForNonStringType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The type to encodeParameter must be string.');
        $this->ethabi->encodeParameter(123, 'value');
    }

    public function testEncodeParameter(): void
    {
        $encoded = $this->ethabi->encodeParameter('uint256', '123');
        $this->assertIsString($encoded);
        $this->assertStringStartsWith('0x', $encoded);
    }

    public function testEncodeParametersWithArrayTypes(): void
    {
        $types = ['uint256', 'string'];
        $params = ['123', 'hello'];

        $encoded = $this->ethabi->encodeParameters($types, $params);
        $this->assertIsString($encoded);
        $this->assertStringStartsWith('0x', $encoded);
    }

    public function testEncodeParametersWithStdClassInputs(): void
    {
        $types = new \stdClass();
        $types->inputs = [
            (object) ['type' => 'uint256'],
            (object) ['type' => 'address'],
        ];
        $params = ['123', '0x1234567890123456789012345678901234567890'];

        $encoded = $this->ethabi->encodeParameters($types, $params);
        $this->assertIsString($encoded);
        $this->assertStringStartsWith('0x', $encoded);
    }

    public function testEncodeParametersWithArrayInputs(): void
    {
        $types = [
            'inputs' => [
                ['type' => 'uint256'],
                ['type' => 'bool'],
            ],
        ];
        $params = ['123', true];

        $encoded = $this->ethabi->encodeParameters($types, $params);
        $this->assertIsString($encoded);
        $this->assertStringStartsWith('0x', $encoded);
    }

    public function testEncodeParametersThrowsExceptionForMismatchedLength(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('encodeParameters number of types must equal to number of params.');

        $types = ['uint256', 'string'];
        $params = ['123']; // Only one param for two types

        $this->ethabi->encodeParameters($types, $params);
    }

    public function testDecodeParameterThrowsExceptionForNonStringType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The type to decodeParameter must be string.');
        $this->ethabi->decodeParameter(123, '0x123');
    }

    public function testDecodeParameter(): void
    {
        // First encode something to get valid data to decode
        $encoded = $this->ethabi->encodeParameter('uint256', '123');
        $decoded = $this->ethabi->decodeParameter('uint256', $encoded);
        $this->assertNotNull($decoded);
    }

    public function testDecodeParametersThrowsExceptionForNonStringParam(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The type or param to decodeParameters must be string.');
        $this->ethabi->decodeParameters(['uint256'], 123);
    }

    public function testDecodeParametersWithArrayTypes(): void
    {
        $types = ['uint256'];
        $encoded = $this->ethabi->encodeParameters($types, ['123']);

        $decoded = $this->ethabi->decodeParameters($types, $encoded);
        $this->assertIsArray($decoded);
    }

    public function testDecodeParametersWithStdClassOutputs(): void
    {
        $types = new \stdClass();
        $types->outputs = [
            (object) ['type' => 'uint256', 'name' => 'value'],
        ];
        $encoded = $this->ethabi->encodeParameter('uint256', '123');

        $decoded = $this->ethabi->decodeParameters($types, $encoded);
        $this->assertIsArray($decoded);
        $this->assertArrayHasKey('value', $decoded);
    }

    public function testDecodeParametersWithArrayOutputs(): void
    {
        $types = [
            'outputs' => [
                ['type' => 'uint256', 'name' => 'amount'],
                ['type' => 'bool', 'name' => 'success'],
            ],
        ];
        $encoded = $this->ethabi->encodeParameters(['uint256', 'bool'], ['123', true]);

        $decoded = $this->ethabi->decodeParameters($types, $encoded);
        $this->assertIsArray($decoded);
        $this->assertArrayHasKey('amount', $decoded);
        $this->assertArrayHasKey('success', $decoded);
    }

    public function testGetSolidityTypesThrowsExceptionForNonArray(): void
    {
        $reflection = new \ReflectionClass($this->ethabi);
        $method = $reflection->getMethod('getSolidityTypes');
        $method->setAccessible(true);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Types must be array');
        $method->invokeArgs($this->ethabi, ['invalid']);
    }

    public function testGetSolidityTypes(): void
    {
        $reflection = new \ReflectionClass($this->ethabi);
        $method = $reflection->getMethod('getSolidityTypes');
        $method->setAccessible(true);

        $types = ['uint256', 'address', 'bool'];
        $result = $method->invokeArgs($this->ethabi, [$types]);

        $this->assertIsArray($result);
        $this->assertCount(3, $result);
        $this->assertInstanceOf(Uinteger::class, $result[0]);
        $this->assertInstanceOf(Address::class, $result[1]);
        $this->assertInstanceOf(Boolean::class, $result[2]);
    }

    public function testGetSolidityTypesWithDynamicBytes(): void
    {
        $reflection = new \ReflectionClass($this->ethabi);
        $method = $reflection->getMethod('getSolidityTypes');
        $method->setAccessible(true);

        $types = ['bytes']; // This should resolve to DynamicBytes
        $result = $method->invokeArgs($this->ethabi, [$types]);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertInstanceOf(DynamicBytes::class, $result[0]);
    }

    public function testGetSolidityTypesThrowsExceptionForUnsupportedType(): void
    {
        $reflection = new \ReflectionClass($this->ethabi);
        $method = $reflection->getMethod('getSolidityTypes');
        $method->setAccessible(true);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupport solidity parameter type: unsupported');
        $method->invokeArgs($this->ethabi, [['unsupported']]);
    }

    public function testEncodeMultiWithOffset(): void
    {
        $reflection = new \ReflectionClass($this->ethabi);
        $method = $reflection->getMethod('encodeMultiWithOffset');
        $method->setAccessible(true);

        $types = ['uint256', 'bool'];
        $solidityTypes = [new Uinteger(), new Boolean()];
        $encodes = ['encoded1', 'encoded2'];

        $result = $method->invokeArgs($this->ethabi, [$types, $solidityTypes, $encodes, 64]);
        $this->assertIsString($result);
    }
}
