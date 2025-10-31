<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Methods\Shh;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Methods\EthMethod;
use Tourze\Web3PHP\Methods\IMethod;
use Tourze\Web3PHP\Methods\IRPC;
use Tourze\Web3PHP\Methods\Shh\AddToGroup;

/**
 * AddToGroup 测试
 * @internal
 */
#[CoversClass(AddToGroup::class)]
final class AddToGroupTest extends TestCase
{
    /**
     * 测试构造函数
     */
    public function testConstruct(): void
    {
        $addToGroup = new AddToGroup();

        $this->assertInstanceOf(AddToGroup::class, $addToGroup);
        $this->assertSame('shh_addToGroup', (string) $addToGroup);
    }

    /**
     * 测试默认属性值
     */
    public function testDefaultProperties(): void
    {
        $addToGroup = new AddToGroup();

        $reflection = new \ReflectionClass($addToGroup);

        // 检查validators属性
        $validators = $reflection->getProperty('validators');
        $validators->setAccessible(true);
        $this->assertSame([
            'Tourze\Web3PHP\Validators\IdentityValidator',
        ], $validators->getValue($addToGroup));

        // 检查inputFormatters属性为空数组
        $inputFormatters = $reflection->getProperty('inputFormatters');
        $inputFormatters->setAccessible(true);
        $this->assertSame([], $inputFormatters->getValue($addToGroup));

        // 检查outputFormatters属性为空数组
        $outputFormatters = $reflection->getProperty('outputFormatters');
        $outputFormatters->setAccessible(true);
        $this->assertSame([], $outputFormatters->getValue($addToGroup));

        // 检查defaultValues属性为空数组
        $defaultValues = $reflection->getProperty('defaultValues');
        $defaultValues->setAccessible(true);
        $this->assertSame([], $defaultValues->getValue($addToGroup));
    }

    /**
     * 测试toPayload方法
     */
    public function testToPayload(): void
    {
        $addToGroup = new AddToGroup();

        $payload = $addToGroup->toPayload();

        $this->assertIsArray($payload);
        $this->assertArrayHasKey('method', $payload);
        $this->assertArrayHasKey('params', $payload);
        $this->assertSame('shh_addToGroup', $payload['method']);
        $this->assertSame([], $payload['params']);
    }

    /**
     * 测试toPayloadString方法
     */
    public function testToPayloadString(): void
    {
        $addToGroup = new AddToGroup();

        $payloadString = $addToGroup->toPayloadString();

        $this->assertIsString($payloadString);
        $this->assertStringContainsString('shh_addToGroup', $payloadString);

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
        $addToGroup = new AddToGroup();

        $this->assertInstanceOf(EthMethod::class, $addToGroup);
        $this->assertInstanceOf(IMethod::class, $addToGroup);
        $this->assertInstanceOf(IRPC::class, $addToGroup);
    }
}
