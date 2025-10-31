<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Methods\Shh;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Methods\EthMethod;
use Tourze\Web3PHP\Methods\IMethod;
use Tourze\Web3PHP\Methods\IRPC;
use Tourze\Web3PHP\Methods\Shh\GetFilterChanges;

/**
 * GetFilterChanges 测试
 * @internal
 */
#[CoversClass(GetFilterChanges::class)]
final class GetFilterChangesTest extends TestCase
{
    /**
     * 测试构造函数
     */
    public function testConstruct(): void
    {
        $getFilterChanges = new GetFilterChanges();

        $this->assertInstanceOf(GetFilterChanges::class, $getFilterChanges);
        $this->assertSame('shh_getFilterChanges', (string) $getFilterChanges);
    }

    /**
     * 测试默认属性值
     */
    public function testDefaultProperties(): void
    {
        $getFilterChanges = new GetFilterChanges();

        $reflection = new \ReflectionClass($getFilterChanges);

        // 检查validators属性
        $validators = $reflection->getProperty('validators');
        $validators->setAccessible(true);
        $this->assertSame([
            'Tourze\Web3PHP\Validators\QuantityValidator',
        ], $validators->getValue($getFilterChanges));

        // 检查inputFormatters属性
        $inputFormatters = $reflection->getProperty('inputFormatters');
        $inputFormatters->setAccessible(true);
        $this->assertSame([
            'Tourze\Web3PHP\Formatters\QuantityFormatter',
        ], $inputFormatters->getValue($getFilterChanges));

        // 检查outputFormatters属性为空数组
        $outputFormatters = $reflection->getProperty('outputFormatters');
        $outputFormatters->setAccessible(true);
        $this->assertSame([], $outputFormatters->getValue($getFilterChanges));

        // 检查defaultValues属性为空数组
        $defaultValues = $reflection->getProperty('defaultValues');
        $defaultValues->setAccessible(true);
        $this->assertSame([], $defaultValues->getValue($getFilterChanges));
    }

    /**
     * 测试toPayload方法
     */
    public function testToPayload(): void
    {
        $getFilterChanges = new GetFilterChanges();

        $payload = $getFilterChanges->toPayload();

        $this->assertIsArray($payload);
        $this->assertArrayHasKey('method', $payload);
        $this->assertArrayHasKey('params', $payload);
        $this->assertSame('shh_getFilterChanges', $payload['method']);
        $this->assertSame([], $payload['params']);
    }

    /**
     * 测试toPayloadString方法
     */
    public function testToPayloadString(): void
    {
        $getFilterChanges = new GetFilterChanges();

        $payloadString = $getFilterChanges->toPayloadString();

        $this->assertIsString($payloadString);
        $this->assertStringContainsString('shh_getFilterChanges', $payloadString);

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
        $getFilterChanges = new GetFilterChanges();

        $this->assertInstanceOf(EthMethod::class, $getFilterChanges);
        $this->assertInstanceOf(IMethod::class, $getFilterChanges);
        $this->assertInstanceOf(IRPC::class, $getFilterChanges);
    }
}
