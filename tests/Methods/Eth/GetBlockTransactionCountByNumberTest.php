<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Methods\Eth;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Formatters\BigNumberFormatter;
use Tourze\Web3PHP\Formatters\OptionalQuantityFormatter;
use Tourze\Web3PHP\Methods\Eth\GetBlockTransactionCountByNumber;
use Tourze\Web3PHP\Methods\EthMethod;
use Tourze\Web3PHP\Methods\IMethod;
use Tourze\Web3PHP\Methods\IRPC;
use Tourze\Web3PHP\Validators\QuantityValidator;

/**
 * GetBlockTransactionCountByNumber 测试
 * @internal
 */
#[CoversClass(GetBlockTransactionCountByNumber::class)]
final class GetBlockTransactionCountByNumberTest extends TestCase
{
    /**
     * 测试构造函数
     */
    public function testConstruct(): void
    {
        $method = new GetBlockTransactionCountByNumber();

        $this->assertInstanceOf(GetBlockTransactionCountByNumber::class, $method);
        $this->assertSame('eth__get_block_transaction_count_by_number', (string) $method);
    }

    /**
     * 测试默认属性值
     */
    public function testDefaultProperties(): void
    {
        $method = new GetBlockTransactionCountByNumber();

        $reflection = new \ReflectionClass($method);

        // 检查validators属性
        $validators = $reflection->getProperty('validators');
        $validators->setAccessible(true);
        $this->assertSame([QuantityValidator::class], $validators->getValue($method));

        // 检查inputFormatters属性
        $inputFormatters = $reflection->getProperty('inputFormatters');
        $inputFormatters->setAccessible(true);
        $this->assertSame([OptionalQuantityFormatter::class], $inputFormatters->getValue($method));

        // 检查outputFormatters属性
        $outputFormatters = $reflection->getProperty('outputFormatters');
        $outputFormatters->setAccessible(true);
        $this->assertSame([BigNumberFormatter::class], $outputFormatters->getValue($method));

        // 检查defaultValues属性
        $defaultValues = $reflection->getProperty('defaultValues');
        $defaultValues->setAccessible(true);
        $this->assertSame(['latest'], $defaultValues->getValue($method));
    }

    /**
     * 测试toPayload方法
     */
    public function testToPayload(): void
    {
        $method = new GetBlockTransactionCountByNumber();

        $payload = $method->toPayload();

        $this->assertIsArray($payload);
        $this->assertArrayHasKey('method', $payload);
        $this->assertArrayHasKey('params', $payload);
        $this->assertSame('eth__get_block_transaction_count_by_number', $payload['method']);
        $this->assertSame([], $payload['params']);
    }

    /**
     * 测试toPayloadString方法
     */
    public function testToPayloadString(): void
    {
        $method = new GetBlockTransactionCountByNumber();

        $payloadString = $method->toPayloadString();

        $this->assertIsString($payloadString);
        $this->assertStringContainsString('eth__get_block_transaction_count_by_number', $payloadString);

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
        $method = new GetBlockTransactionCountByNumber();

        $this->assertInstanceOf(EthMethod::class, $method);
        $this->assertInstanceOf(IMethod::class, $method);
        $this->assertInstanceOf(IRPC::class, $method);
    }
}
