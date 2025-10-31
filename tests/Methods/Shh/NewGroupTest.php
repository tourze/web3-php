<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Methods\Shh;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Methods\EthMethod;
use Tourze\Web3PHP\Methods\IMethod;
use Tourze\Web3PHP\Methods\IRPC;
use Tourze\Web3PHP\Methods\Shh\NewGroup;

/**
 * NewGroup 测试
 * @internal
 */
#[CoversClass(NewGroup::class)]
final class NewGroupTest extends TestCase
{
    /**
     * 测试构造函数
     */
    public function testConstruct(): void
    {
        $newGroup = new NewGroup();

        $this->assertInstanceOf(NewGroup::class, $newGroup);
        $this->assertSame('shh_newGroup', (string) $newGroup);
    }

    /**
     * 测试默认属性值
     */
    public function testDefaultProperties(): void
    {
        $newGroup = new NewGroup();

        $reflection = new \ReflectionClass($newGroup);

        // 检查validators属性为空数组
        $validators = $reflection->getProperty('validators');
        $validators->setAccessible(true);
        $this->assertSame([], $validators->getValue($newGroup));

        // 检查inputFormatters属性为空数组
        $inputFormatters = $reflection->getProperty('inputFormatters');
        $inputFormatters->setAccessible(true);
        $this->assertSame([], $inputFormatters->getValue($newGroup));

        // 检查outputFormatters属性为空数组
        $outputFormatters = $reflection->getProperty('outputFormatters');
        $outputFormatters->setAccessible(true);
        $this->assertSame([], $outputFormatters->getValue($newGroup));

        // 检查defaultValues属性为空数组
        $defaultValues = $reflection->getProperty('defaultValues');
        $defaultValues->setAccessible(true);
        $this->assertSame([], $defaultValues->getValue($newGroup));
    }

    /**
     * 测试toPayload方法
     */
    public function testToPayload(): void
    {
        $newGroup = new NewGroup();

        $payload = $newGroup->toPayload();

        $this->assertIsArray($payload);
        $this->assertArrayHasKey('method', $payload);
        $this->assertArrayHasKey('params', $payload);
        $this->assertSame('shh_newGroup', $payload['method']);
        $this->assertSame([], $payload['params']);
    }

    /**
     * 测试toPayloadString方法
     */
    public function testToPayloadString(): void
    {
        $newGroup = new NewGroup();

        $payloadString = $newGroup->toPayloadString();

        $this->assertIsString($payloadString);
        $this->assertStringContainsString('shh_newGroup', $payloadString);

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
        $newGroup = new NewGroup();

        $this->assertInstanceOf(EthMethod::class, $newGroup);
        $this->assertInstanceOf(IMethod::class, $newGroup);
        $this->assertInstanceOf(IRPC::class, $newGroup);
    }
}
