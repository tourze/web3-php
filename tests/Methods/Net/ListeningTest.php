<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Methods\Net;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Methods\EthMethod;
use Tourze\Web3PHP\Methods\Net\Listening;

/**
 * Listening 测试
 * @internal
 */
#[CoversClass(Listening::class)]
final class ListeningTest extends TestCase
{
    /**
     * 测试构造函数
     */
    public function testConstruct(): void
    {
        $listening = new Listening();

        $this->assertInstanceOf(Listening::class, $listening);
        $this->assertInstanceOf(EthMethod::class, $listening);
        $this->assertSame('net_listening', (string) $listening);
    }

    /**
     * 测试默认属性值
     */
    public function testDefaultProperties(): void
    {
        $listening = new Listening();

        $reflection = new \ReflectionClass($listening);

        // 检查validators属性为空数组
        $validators = $reflection->getProperty('validators');
        $validators->setAccessible(true);
        $this->assertSame([], $validators->getValue($listening));

        // 检查inputFormatters属性为空数组
        $inputFormatters = $reflection->getProperty('inputFormatters');
        $inputFormatters->setAccessible(true);
        $this->assertSame([], $inputFormatters->getValue($listening));

        // 检查outputFormatters属性为空数组
        $outputFormatters = $reflection->getProperty('outputFormatters');
        $outputFormatters->setAccessible(true);
        $this->assertSame([], $outputFormatters->getValue($listening));

        // 检查defaultValues属性为空数组
        $defaultValues = $reflection->getProperty('defaultValues');
        $defaultValues->setAccessible(true);
        $this->assertSame([], $defaultValues->getValue($listening));
    }

    /**
     * 测试toPayload方法
     */
    public function testToPayload(): void
    {
        $listening = new Listening();

        $payload = $listening->toPayload();

        $this->assertIsArray($payload);
        $this->assertArrayHasKey('method', $payload);
        $this->assertArrayHasKey('params', $payload);
        $this->assertSame('net_listening', $payload['method']);
        $this->assertSame([], $payload['params']);
    }

    /**
     * 测试toPayloadString方法
     */
    public function testToPayloadString(): void
    {
        $listening = new Listening();

        $payloadString = $listening->toPayloadString();

        $this->assertIsString($payloadString);
        $this->assertStringContainsString('net_listening', $payloadString);

        // 验证是有效的JSON
        $decoded = json_decode($payloadString, true);
        $this->assertIsArray($decoded);
        $this->assertArrayHasKey('method', $decoded);
        $this->assertArrayHasKey('params', $decoded);
    }

    /**
     * 测试继承关系
     */
    public function testInheritance(): void
    {
        $listening = new Listening();

        $this->assertInstanceOf(EthMethod::class, $listening);
    }
}
