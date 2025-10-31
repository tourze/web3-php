<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Methods\Net;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Methods\EthMethod;
use Tourze\Web3PHP\Methods\Net\PeerCount;

/**
 * PeerCount 测试
 * @internal
 */
#[CoversClass(PeerCount::class)]
final class PeerCountTest extends TestCase
{
    /**
     * 测试构造函数
     */
    public function testConstruct(): void
    {
        $peerCount = new PeerCount();

        $this->assertInstanceOf(PeerCount::class, $peerCount);
        $this->assertInstanceOf(EthMethod::class, $peerCount);
        $this->assertSame('net_peerCount', (string) $peerCount);
    }

    /**
     * 测试默认属性
     */
    public function testDefaultProperties(): void
    {
        $peerCount = new PeerCount();

        $this->assertSame('net_peerCount', (string) $peerCount);
        $payload = $peerCount->toPayload();
        $this->assertSame([], $payload['params']);
    }

    /**
     * 测试toPayload方法
     */
    public function testToPayload(): void
    {
        $peerCount = new PeerCount();

        $payload = $peerCount->toPayload();

        $this->assertIsArray($payload);
        $this->assertArrayHasKey('method', $payload);
        $this->assertArrayHasKey('params', $payload);
        $this->assertSame('net_peerCount', $payload['method']);
        $this->assertSame([], $payload['params']);
    }

    /**
     * 测试toPayloadString方法
     */
    public function testToPayloadString(): void
    {
        $peerCount = new PeerCount();

        $payloadString = $peerCount->toPayloadString();

        $this->assertIsString($payloadString);
        $this->assertStringContainsString('net_peerCount', $payloadString);
    }

    /**
     * 测试继承关系
     */
    public function testInheritance(): void
    {
        $peerCount = new PeerCount();

        $this->assertInstanceOf(EthMethod::class, $peerCount);
    }
}
