<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Methods\Shh;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Methods\EthMethod;
use Tourze\Web3PHP\Methods\IMethod;
use Tourze\Web3PHP\Methods\IRPC;
use Tourze\Web3PHP\Methods\Shh\GetMessages;

/**
 * GetMessages 测试
 * @internal
 */
#[CoversClass(GetMessages::class)]
final class GetMessagesTest extends TestCase
{
    /**
     * 测试构造函数
     */
    public function testConstruct(): void
    {
        $getMessages = new GetMessages();

        $this->assertInstanceOf(GetMessages::class, $getMessages);
        $this->assertSame('shh_getMessages', (string) $getMessages);
    }

    /**
     * 测试默认属性值
     */
    public function testDefaultProperties(): void
    {
        $getMessages = new GetMessages();

        $reflection = new \ReflectionClass($getMessages);

        // 检查validators属性
        $validators = $reflection->getProperty('validators');
        $validators->setAccessible(true);
        $this->assertSame([
            'Tourze\Web3PHP\Validators\QuantityValidator',
        ], $validators->getValue($getMessages));

        // 检查inputFormatters属性
        $inputFormatters = $reflection->getProperty('inputFormatters');
        $inputFormatters->setAccessible(true);
        $this->assertSame([
            'Tourze\Web3PHP\Formatters\QuantityFormatter',
        ], $inputFormatters->getValue($getMessages));

        // 检查outputFormatters属性为空数组
        $outputFormatters = $reflection->getProperty('outputFormatters');
        $outputFormatters->setAccessible(true);
        $this->assertSame([], $outputFormatters->getValue($getMessages));

        // 检查defaultValues属性为空数组
        $defaultValues = $reflection->getProperty('defaultValues');
        $defaultValues->setAccessible(true);
        $this->assertSame([], $defaultValues->getValue($getMessages));
    }

    /**
     * 测试toPayload方法
     */
    public function testToPayload(): void
    {
        $getMessages = new GetMessages();

        $payload = $getMessages->toPayload();

        $this->assertIsArray($payload);
        $this->assertArrayHasKey('method', $payload);
        $this->assertArrayHasKey('params', $payload);
        $this->assertSame('shh_getMessages', $payload['method']);
        $this->assertSame([], $payload['params']);
    }

    /**
     * 测试toPayloadString方法
     */
    public function testToPayloadString(): void
    {
        $getMessages = new GetMessages();

        $payloadString = $getMessages->toPayloadString();

        $this->assertIsString($payloadString);
        $this->assertStringContainsString('shh_getMessages', $payloadString);

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
        $getMessages = new GetMessages();

        $this->assertInstanceOf(EthMethod::class, $getMessages);
        $this->assertInstanceOf(IMethod::class, $getMessages);
        $this->assertInstanceOf(IRPC::class, $getMessages);
    }
}
