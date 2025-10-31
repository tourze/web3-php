<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Methods\Eth;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Formatters\HexFormatter;
use Tourze\Web3PHP\Formatters\QuantityFormatter;
use Tourze\Web3PHP\Methods\Eth\GetTransactionByBlockHashAndIndex;
use Tourze\Web3PHP\Methods\EthMethod;
use Tourze\Web3PHP\Methods\IMethod;
use Tourze\Web3PHP\Methods\IRPC;
use Tourze\Web3PHP\Validators\BlockHashValidator;
use Tourze\Web3PHP\Validators\QuantityValidator;

/**
 * GetTransactionByBlockHashAndIndex 测试
 * @internal
 */
#[CoversClass(GetTransactionByBlockHashAndIndex::class)]
final class GetTransactionByBlockHashAndIndexTest extends TestCase
{
    /**
     * 测试构造函数
     */
    public function testConstruct(): void
    {
        $method = new GetTransactionByBlockHashAndIndex();

        $this->assertInstanceOf(GetTransactionByBlockHashAndIndex::class, $method);
        $this->assertSame('eth__get_transaction_by_block_hash_and_index', (string) $method);
    }

    /**
     * 测试默认属性值
     */
    public function testDefaultProperties(): void
    {
        $method = new GetTransactionByBlockHashAndIndex();

        $reflection = new \ReflectionClass($method);

        // 检查validators属性
        $validators = $reflection->getProperty('validators');
        $validators->setAccessible(true);
        $this->assertSame([
            BlockHashValidator::class, QuantityValidator::class,
        ], $validators->getValue($method));

        // 检查inputFormatters属性
        $inputFormatters = $reflection->getProperty('inputFormatters');
        $inputFormatters->setAccessible(true);
        $this->assertSame([
            HexFormatter::class, QuantityFormatter::class,
        ], $inputFormatters->getValue($method));

        // 检查outputFormatters属性为空数组
        $outputFormatters = $reflection->getProperty('outputFormatters');
        $outputFormatters->setAccessible(true);
        $this->assertSame([], $outputFormatters->getValue($method));

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
        $method = new GetTransactionByBlockHashAndIndex();

        $payload = $method->toPayload();

        $this->assertIsArray($payload);
        $this->assertArrayHasKey('method', $payload);
        $this->assertArrayHasKey('params', $payload);
        $this->assertSame('eth__get_transaction_by_block_hash_and_index', $payload['method']);
        $this->assertSame([], $payload['params']);
    }

    /**
     * 测试toPayloadString方法
     */
    public function testToPayloadString(): void
    {
        $method = new GetTransactionByBlockHashAndIndex();

        $payloadString = $method->toPayloadString();

        $this->assertIsString($payloadString);
        $this->assertStringContainsString('eth__get_transaction_by_block_hash_and_index', $payloadString);

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
        $method = new GetTransactionByBlockHashAndIndex();

        $this->assertInstanceOf(EthMethod::class, $method);
        $this->assertInstanceOf(IMethod::class, $method);
        $this->assertInstanceOf(IRPC::class, $method);
    }
}
