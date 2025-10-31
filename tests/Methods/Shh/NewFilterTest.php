<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Methods\Shh;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Methods\EthMethod;
use Tourze\Web3PHP\Methods\IMethod;
use Tourze\Web3PHP\Methods\IRPC;
use Tourze\Web3PHP\Methods\Shh\NewFilter;

/**
 * NewFilter 测试
 * @internal
 */
#[CoversClass(NewFilter::class)]
final class NewFilterTest extends TestCase
{
    /**
     * 测试构造函数
     */
    public function testConstruct(): void
    {
        $newFilter = new NewFilter();

        $this->assertInstanceOf(NewFilter::class, $newFilter);
        $this->assertSame('shh_newFilter', (string) $newFilter);
    }

    /**
     * 测试默认属性值
     */
    public function testDefaultProperties(): void
    {
        $newFilter = new NewFilter();

        $reflection = new \ReflectionClass($newFilter);

        // 检查validators属性
        $validators = $reflection->getProperty('validators');
        $validators->setAccessible(true);
        $this->assertSame([
            'Tourze\Web3PHP\Validators\ShhFilterValidator',
        ], $validators->getValue($newFilter));

        // 检查inputFormatters属性为空数组
        $inputFormatters = $reflection->getProperty('inputFormatters');
        $inputFormatters->setAccessible(true);
        $this->assertSame([], $inputFormatters->getValue($newFilter));

        // 检查outputFormatters属性为空数组
        $outputFormatters = $reflection->getProperty('outputFormatters');
        $outputFormatters->setAccessible(true);
        $this->assertSame([], $outputFormatters->getValue($newFilter));

        // 检查defaultValues属性为空数组
        $defaultValues = $reflection->getProperty('defaultValues');
        $defaultValues->setAccessible(true);
        $this->assertSame([], $defaultValues->getValue($newFilter));
    }

    /**
     * 测试toPayload方法
     */
    public function testToPayload(): void
    {
        $newFilter = new NewFilter();

        $payload = $newFilter->toPayload();

        $this->assertIsArray($payload);
        $this->assertArrayHasKey('method', $payload);
        $this->assertArrayHasKey('params', $payload);
        $this->assertSame('shh_newFilter', $payload['method']);
        $this->assertSame([], $payload['params']);
    }

    /**
     * 测试toPayloadString方法
     */
    public function testToPayloadString(): void
    {
        $newFilter = new NewFilter();

        $payloadString = $newFilter->toPayloadString();

        $this->assertIsString($payloadString);
        $this->assertStringContainsString('shh_newFilter', $payloadString);

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
        $newFilter = new NewFilter();

        $this->assertInstanceOf(EthMethod::class, $newFilter);
        $this->assertInstanceOf(IMethod::class, $newFilter);
        $this->assertInstanceOf(IRPC::class, $newFilter);
    }
}
