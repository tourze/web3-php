<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Methods\Shh;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Methods\EthMethod;
use Tourze\Web3PHP\Methods\IMethod;
use Tourze\Web3PHP\Methods\IRPC;
use Tourze\Web3PHP\Methods\Shh\HasIdentity;

/**
 * HasIdentity 测试
 * @internal
 */
#[CoversClass(HasIdentity::class)]
final class HasIdentityTest extends TestCase
{
    /**
     * 测试构造函数
     */
    public function testConstruct(): void
    {
        $hasIdentity = new HasIdentity();

        $this->assertInstanceOf(HasIdentity::class, $hasIdentity);
        $this->assertSame('shh_hasIdentity', (string) $hasIdentity);
    }

    /**
     * 测试默认属性值
     */
    public function testDefaultProperties(): void
    {
        $hasIdentity = new HasIdentity();

        $reflection = new \ReflectionClass($hasIdentity);

        // 检查validators属性
        $validators = $reflection->getProperty('validators');
        $validators->setAccessible(true);
        $this->assertSame([
            'Tourze\Web3PHP\Validators\IdentityValidator',
        ], $validators->getValue($hasIdentity));

        // 检查inputFormatters属性为空数组
        $inputFormatters = $reflection->getProperty('inputFormatters');
        $inputFormatters->setAccessible(true);
        $this->assertSame([], $inputFormatters->getValue($hasIdentity));

        // 检查outputFormatters属性为空数组
        $outputFormatters = $reflection->getProperty('outputFormatters');
        $outputFormatters->setAccessible(true);
        $this->assertSame([], $outputFormatters->getValue($hasIdentity));

        // 检查defaultValues属性为空数组
        $defaultValues = $reflection->getProperty('defaultValues');
        $defaultValues->setAccessible(true);
        $this->assertSame([], $defaultValues->getValue($hasIdentity));
    }

    /**
     * 测试toPayload方法
     */
    public function testToPayload(): void
    {
        $hasIdentity = new HasIdentity();

        $payload = $hasIdentity->toPayload();

        $this->assertIsArray($payload);
        $this->assertArrayHasKey('method', $payload);
        $this->assertArrayHasKey('params', $payload);
        $this->assertSame('shh_hasIdentity', $payload['method']);
        $this->assertSame([], $payload['params']);
    }

    /**
     * 测试toPayloadString方法
     */
    public function testToPayloadString(): void
    {
        $hasIdentity = new HasIdentity();

        $payloadString = $hasIdentity->toPayloadString();

        $this->assertIsString($payloadString);
        $this->assertStringContainsString('shh_hasIdentity', $payloadString);

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
        $hasIdentity = new HasIdentity();

        $this->assertInstanceOf(EthMethod::class, $hasIdentity);
        $this->assertInstanceOf(IMethod::class, $hasIdentity);
        $this->assertInstanceOf(IRPC::class, $hasIdentity);
    }
}
