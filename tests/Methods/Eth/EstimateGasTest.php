<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Methods\Eth;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Formatters\BigNumberFormatter;
use Tourze\Web3PHP\Formatters\TransactionFormatter;
use Tourze\Web3PHP\Methods\Eth\EstimateGas;
use Tourze\Web3PHP\Methods\EthMethod;
use Tourze\Web3PHP\Methods\IMethod;
use Tourze\Web3PHP\Methods\IRPC;
use Tourze\Web3PHP\Validators\TransactionValidator;

/**
 * EstimateGas 测试
 * @internal
 */
#[CoversClass(EstimateGas::class)]
final class EstimateGasTest extends TestCase
{
    /**
     * 测试构造函数
     */
    public function testConstruct(): void
    {
        $method = new EstimateGas();

        $this->assertInstanceOf(EstimateGas::class, $method);
        $this->assertSame('eth_estimateGas', (string) $method);
    }

    /**
     * 测试默认属性值
     */
    public function testDefaultProperties(): void
    {
        $method = new EstimateGas();

        $reflection = new \ReflectionClass($method);

        // 检查validators属性包含TransactionValidator
        $validators = $reflection->getProperty('validators');
        $validators->setAccessible(true);
        $this->assertSame([TransactionValidator::class], $validators->getValue($method));

        // 检查inputFormatters属性包含TransactionFormatter
        $inputFormatters = $reflection->getProperty('inputFormatters');
        $inputFormatters->setAccessible(true);
        $this->assertSame([TransactionFormatter::class], $inputFormatters->getValue($method));

        // 检查outputFormatters属性包含BigNumberFormatter
        $outputFormatters = $reflection->getProperty('outputFormatters');
        $outputFormatters->setAccessible(true);
        $this->assertSame([BigNumberFormatter::class], $outputFormatters->getValue($method));

        // 检查defaultValues属性为空数组
        $defaultValues = $reflection->getProperty('defaultValues');
        $defaultValues->setAccessible(true);
        $this->assertSame([], $defaultValues->getValue($method));
    }

    /**
     * 测试toPayload方法
     */
    public function testToPayload(): void
    {
        $method = new EstimateGas();

        $payload = $method->toPayload();

        $this->assertIsArray($payload);
        $this->assertArrayHasKey('method', $payload);
        $this->assertArrayHasKey('params', $payload);
        $this->assertSame('eth_estimateGas', $payload['method']);
        $this->assertSame([], $payload['params']);
    }

    /**
     * 测试toPayloadString方法
     */
    public function testToPayloadString(): void
    {
        $method = new EstimateGas();

        $payloadString = $method->toPayloadString();

        $this->assertIsString($payloadString);
        $this->assertStringContainsString('eth_estimateGas', $payloadString);

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
        $method = new EstimateGas();

        $this->assertInstanceOf(EthMethod::class, $method);
        $this->assertInstanceOf(IMethod::class, $method);
        $this->assertInstanceOf(IRPC::class, $method);
    }
}
