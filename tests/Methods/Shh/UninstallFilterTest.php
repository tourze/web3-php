<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Methods\Shh;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Methods\EthMethod;
use Tourze\Web3PHP\Methods\IMethod;
use Tourze\Web3PHP\Methods\IRPC;
use Tourze\Web3PHP\Methods\Shh\UninstallFilter;

/**
 * UninstallFilter 测试
 * @internal
 */
#[CoversClass(UninstallFilter::class)]
final class UninstallFilterTest extends TestCase
{
    /**
     * 测试构造函数
     */
    public function testConstruct(): void
    {
        $uninstallFilter = new UninstallFilter();

        $this->assertInstanceOf(UninstallFilter::class, $uninstallFilter);
        $this->assertSame('shh_uninstallFilter', (string) $uninstallFilter);
    }

    /**
     * 测试默认属性值
     */
    public function testDefaultProperties(): void
    {
        $uninstallFilter = new UninstallFilter();

        $reflection = new \ReflectionClass($uninstallFilter);

        // 检查validators属性
        $validators = $reflection->getProperty('validators');
        $validators->setAccessible(true);
        $this->assertSame([
            'Tourze\Web3PHP\Validators\QuantityValidator',
        ], $validators->getValue($uninstallFilter));

        // 检查inputFormatters属性
        $inputFormatters = $reflection->getProperty('inputFormatters');
        $inputFormatters->setAccessible(true);
        $this->assertSame([
            'Tourze\Web3PHP\Formatters\QuantityFormatter',
        ], $inputFormatters->getValue($uninstallFilter));

        // 检查outputFormatters属性为空数组
        $outputFormatters = $reflection->getProperty('outputFormatters');
        $outputFormatters->setAccessible(true);
        $this->assertSame([], $outputFormatters->getValue($uninstallFilter));

        // 检查defaultValues属性为空数组
        $defaultValues = $reflection->getProperty('defaultValues');
        $defaultValues->setAccessible(true);
        $this->assertSame([], $defaultValues->getValue($uninstallFilter));
    }

    /**
     * 测试toPayload方法
     */
    public function testToPayload(): void
    {
        $uninstallFilter = new UninstallFilter();

        $payload = $uninstallFilter->toPayload();

        $this->assertIsArray($payload);
        $this->assertArrayHasKey('method', $payload);
        $this->assertArrayHasKey('params', $payload);
        $this->assertSame('shh_uninstallFilter', $payload['method']);
        $this->assertSame([], $payload['params']);
    }

    /**
     * 测试toPayloadString方法
     */
    public function testToPayloadString(): void
    {
        $uninstallFilter = new UninstallFilter();

        $payloadString = $uninstallFilter->toPayloadString();

        $this->assertIsString($payloadString);
        $this->assertStringContainsString('shh_uninstallFilter', $payloadString);

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
        $uninstallFilter = new UninstallFilter();

        $this->assertInstanceOf(EthMethod::class, $uninstallFilter);
        $this->assertInstanceOf(IMethod::class, $uninstallFilter);
        $this->assertInstanceOf(IRPC::class, $uninstallFilter);
    }
}
