<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Methods\Web3;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Methods\EthMethod;
use Tourze\Web3PHP\Methods\IMethod;
use Tourze\Web3PHP\Methods\IRPC;
use Tourze\Web3PHP\Methods\Web3\ClientVersion;

/**
 * ClientVersion 测试
 * @internal
 */
#[CoversClass(ClientVersion::class)]
final class ClientVersionTest extends TestCase
{
    /**
     * 测试构造函数
     */
    public function testConstruct(): void
    {
        $clientVersion = new ClientVersion();

        $this->assertInstanceOf(ClientVersion::class, $clientVersion);
        $this->assertSame('web3_clientVersion', (string) $clientVersion);
    }

    /**
     * 测试默认属性值
     */
    public function testDefaultProperties(): void
    {
        $clientVersion = new ClientVersion();

        $reflection = new \ReflectionClass($clientVersion);

        // 检查validators属性为空数组
        $validators = $reflection->getProperty('validators');
        $validators->setAccessible(true);
        $this->assertSame([], $validators->getValue($clientVersion));

        // 检查inputFormatters属性为空数组
        $inputFormatters = $reflection->getProperty('inputFormatters');
        $inputFormatters->setAccessible(true);
        $this->assertSame([], $inputFormatters->getValue($clientVersion));

        // 检查outputFormatters属性为空数组
        $outputFormatters = $reflection->getProperty('outputFormatters');
        $outputFormatters->setAccessible(true);
        $this->assertSame([], $outputFormatters->getValue($clientVersion));

        // 检查defaultValues属性为空数组
        $defaultValues = $reflection->getProperty('defaultValues');
        $defaultValues->setAccessible(true);
        $this->assertSame([], $defaultValues->getValue($clientVersion));
    }

    /**
     * 测试toPayload方法
     */
    public function testToPayload(): void
    {
        $clientVersion = new ClientVersion();

        $payload = $clientVersion->toPayload();

        $this->assertIsArray($payload);
        $this->assertArrayHasKey('method', $payload);
        $this->assertArrayHasKey('params', $payload);
        $this->assertSame('web3_clientVersion', $payload['method']);
        $this->assertSame([], $payload['params']);
    }

    /**
     * 测试toPayloadString方法
     */
    public function testToPayloadString(): void
    {
        $clientVersion = new ClientVersion();

        $payloadString = $clientVersion->toPayloadString();

        $this->assertIsString($payloadString);
        $this->assertStringContainsString('web3_clientVersion', $payloadString);

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
        $clientVersion = new ClientVersion();

        $this->assertInstanceOf(EthMethod::class, $clientVersion);
        $this->assertInstanceOf(IMethod::class, $clientVersion);
        $this->assertInstanceOf(IRPC::class, $clientVersion);
    }
}
