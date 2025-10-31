<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Methods\Eth;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Formatters\AddressFormatter;
use Tourze\Web3PHP\Formatters\OptionalQuantityFormatter;
use Tourze\Web3PHP\Formatters\QuantityFormatter;
use Tourze\Web3PHP\Methods\Eth\GetStorageAt;
use Tourze\Web3PHP\Methods\EthMethod;
use Tourze\Web3PHP\Methods\IMethod;
use Tourze\Web3PHP\Methods\IRPC;
use Tourze\Web3PHP\Validators\AddressValidator;
use Tourze\Web3PHP\Validators\QuantityValidator;
use Tourze\Web3PHP\Validators\TagValidator;

/**
 * GetStorageAt 测试
 * @internal
 */
#[CoversClass(GetStorageAt::class)]
final class GetStorageAtTest extends TestCase
{
    /**
     * 测试构造函数
     */
    public function testConstruct(): void
    {
        $method = new GetStorageAt();

        $this->assertInstanceOf(GetStorageAt::class, $method);
        $this->assertSame('eth__get_storage_at', (string) $method);
    }

    /**
     * 测试默认属性值
     */
    public function testDefaultProperties(): void
    {
        $method = new GetStorageAt();

        $reflection = new \ReflectionClass($method);

        // 检查validators属性
        $validators = $reflection->getProperty('validators');
        $validators->setAccessible(true);
        $this->assertSame([
            AddressValidator::class, QuantityValidator::class, [
                TagValidator::class, QuantityValidator::class,
            ],
        ], $validators->getValue($method));

        // 检查inputFormatters属性
        $inputFormatters = $reflection->getProperty('inputFormatters');
        $inputFormatters->setAccessible(true);
        $this->assertSame([
            AddressFormatter::class, QuantityFormatter::class, OptionalQuantityFormatter::class,
        ], $inputFormatters->getValue($method));

        // 检查outputFormatters属性
        $outputFormatters = $reflection->getProperty('outputFormatters');
        $outputFormatters->setAccessible(true);
        $this->assertSame([], $outputFormatters->getValue($method));

        // 检查defaultValues属性
        $defaultValues = $reflection->getProperty('defaultValues');
        $defaultValues->setAccessible(true);
        $this->assertSame([2 => 'latest'], $defaultValues->getValue($method));
    }

    /**
     * 测试toPayload方法
     */
    public function testToPayload(): void
    {
        $method = new GetStorageAt();

        $payload = $method->toPayload();

        $this->assertIsArray($payload);
        $this->assertArrayHasKey('method', $payload);
        $this->assertArrayHasKey('params', $payload);
        $this->assertSame('eth__get_storage_at', $payload['method']);
        $this->assertSame([], $payload['params']);
    }

    /**
     * 测试toPayloadString方法
     */
    public function testToPayloadString(): void
    {
        $method = new GetStorageAt();

        $payloadString = $method->toPayloadString();

        $this->assertIsString($payloadString);
        $this->assertStringContainsString('eth__get_storage_at', $payloadString);

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
        $method = new GetStorageAt();

        $this->assertInstanceOf(EthMethod::class, $method);
        $this->assertInstanceOf(IMethod::class, $method);
        $this->assertInstanceOf(IRPC::class, $method);
    }
}
