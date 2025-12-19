<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Methods;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Methods\EthMethod;

/**
 * EthMethod 测试
 * @internal
 */
#[CoversClass(EthMethod::class)]
final class EthMethodTest extends TestCase
{
    /**
     * 测试构造函数
     */
    public function testConstruct(): void
    {
        $method = new TestEthMethod('testMethod', ['arg1', 'arg2']);

        $this->assertInstanceOf(EthMethod::class, $method);
    }

    /**
     * 测试__toString方法
     */
    public function testToString(): void
    {
        $method = new TestEthMethod('testMethod', []);

        $this->assertSame('testMethod', (string) $method);
    }

    /**
     * 测试toPayload方法
     */
    public function testToPayload(): void
    {
        $arguments = ['arg1', 'arg2'];
        $method = new TestEthMethod('testMethod', $arguments);

        $payload = $method->toPayload();

        $this->assertIsArray($payload);
        $this->assertArrayHasKey('method', $payload);
        $this->assertArrayHasKey('params', $payload);
        $this->assertSame('testMethod', $payload['method']);
        $this->assertSame($arguments, $payload['params']);
    }

    /**
     * 测试toPayloadString方法
     */
    public function testToPayloadString(): void
    {
        $method = new TestEthMethod('testMethod', ['arg1']);

        $payloadString = $method->toPayloadString();

        $this->assertIsString($payloadString);
        $this->assertStringContainsString('testMethod', $payloadString);
        $this->assertStringContainsString('arg1', $payloadString);
    }

    /**
     * 测试validateArguments方法
     */
    public function testValidateArguments(): void
    {
        $method = new TestEthMethod('testMethod', []);

        // 测试有效的数组参数
        $this->assertTrue($method->validateArguments(['arg1', 'arg2']));
    }

    /**
     * 测试validateArguments方法传入非数组参数
     */
    public function testValidateArgumentsWithNonArray(): void
    {
        $method = new TestEthMethod('testMethod', []);

        // 在 PHP 8.0+ 中，类型不匹配会抛出 TypeError
        $this->expectException(\TypeError::class);
        $method->validateArguments('invalid'); // 传入字符串而不是数组
    }

    /**
     * 测试formatArguments方法
     */
    public function testFormatArguments(): void
    {
        $method = new TestEthMethod('testMethod', []);

        $arguments = ['arg1', 'arg2'];
        $formatted = $method->formatArguments($arguments);

        $this->assertIsArray($formatted);
        $this->assertSame($arguments, $formatted);
    }

    /**
     * 测试formatArguments方法传入非数组参数
     */
    public function testFormatArgumentsWithNonArray(): void
    {
        $method = new TestEthMethod('testMethod', []);

        // 在 PHP 8.0+ 中，类型不匹配会抛出 TypeError
        $this->expectException(\TypeError::class);
        $method->formatArguments('invalid'); // 传入字符串而不是数组
    }

    /**
     * 测试formatOutput方法
     */
    public function testFormatOutput(): void
    {
        $method = new TestEthMethod('testMethod', []);

        $output = 'testOutput';
        $formatted = $method->formatOutput($output);

        $this->assertSame($output, $formatted);
    }

    /**
     * 测试transform方法
     */
    public function testTransform(): void
    {
        $method = new TestEthMethod('testMethod', []);

        $data = ['key1' => 'value1', 'key2' => 'value2'];
        // 使用callable而不是字符串函数名
        $rules = ['key1' => function ($value) {
            return strtoupper($value);
        }];

        $transformed = $method->transform($data, $rules);

        $this->assertIsArray($transformed);
        $this->assertArrayHasKey('key1', $transformed);
        $this->assertArrayHasKey('key2', $transformed);
        $this->assertSame('VALUE1', $transformed['key1']);
        $this->assertSame('value2', $transformed['key2']);
    }
}
