<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Methods\Shh;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Methods\EthMethod;
use Tourze\Web3PHP\Methods\IMethod;
use Tourze\Web3PHP\Methods\IRPC;
use Tourze\Web3PHP\Methods\Shh\Version;

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
        $this->assertSame('shh_version', (string) $version);
    }

    /**
     * 测试默认属性值
     */
    public function testDefaultProperties(): void
    {
        $version = new Version();

        $reflection = new \ReflectionClass($version);

        // 检查validators属性为空数组
        $validators = $reflection->getProperty('validators');
        $validators->setAccessible(true);
        $this->assertSame([], $validators->getValue($version));

        // 检查inputFormatters属性为空数组
        $inputFormatters = $reflection->getProperty('inputFormatters');
        $inputFormatters->setAccessible(true);
        $this->assertSame([], $inputFormatters->getValue($version));

        // 检查outputFormatters属性为空数组
        $outputFormatters = $reflection->getProperty('outputFormatters');
        $outputFormatters->setAccessible(true);
        $this->assertSame([], $outputFormatters->getValue($version));

        // 检查defaultValues属性为空数组
        $defaultValues = $reflection->getProperty('defaultValues');
        $defaultValues->setAccessible(true);
        $this->assertSame([], $defaultValues->getValue($version));
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
        $this->assertSame('shh_version', $payload['method']);
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
        $this->assertStringContainsString('shh_version', $payloadString);

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
        $version = new Version();

        $this->assertInstanceOf(EthMethod::class, $version);
        $this->assertInstanceOf(IMethod::class, $version);
        $this->assertInstanceOf(IRPC::class, $version);
    }
}
