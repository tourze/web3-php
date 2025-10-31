<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Methods\Net;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Methods\EthMethod;
use Tourze\Web3PHP\Methods\Net\Version;

/**
 * Version 测试
 * @internal
 */
#[CoversClass(Version::class)]
final class VersionTest extends TestCase
{
    /**
     * 测试构造函数
     */
    public function testConstruct(): void
    {
        $version = new Version();

        $this->assertInstanceOf(Version::class, $version);
        $this->assertInstanceOf(EthMethod::class, $version);
        $this->assertSame('net_version', (string) $version);
    }

    /**
     * 测试默认属性
     */
    public function testDefaultProperties(): void
    {
        $version = new Version();

        $this->assertSame('net_version', (string) $version);
        $payload = $version->toPayload();
        $this->assertSame([], $payload['params']);
    }

    /**
     * 测试toPayload方法
     */
    public function testToPayload(): void
    {
        $version = new Version();

        $payload = $version->toPayload();

        $this->assertIsArray($payload);
        $this->assertArrayHasKey('method', $payload);
        $this->assertArrayHasKey('params', $payload);
        $this->assertSame('net_version', $payload['method']);
        $this->assertSame([], $payload['params']);
    }

    /**
     * 测试toPayloadString方法
     */
    public function testToPayloadString(): void
    {
        $version = new Version();

        $payloadString = $version->toPayloadString();

        $this->assertIsString($payloadString);
        $this->assertStringContainsString('net_version', $payloadString);
    }

    /**
     * 测试继承关系
     */
    public function testInheritance(): void
    {
        $version = new Version();

        $this->assertInstanceOf(EthMethod::class, $version);
    }
}
