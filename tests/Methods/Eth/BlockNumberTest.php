<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Methods\Eth;

use phpseclib3\Math\BigInteger;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Formatters\BigNumberFormatter;
use Tourze\Web3PHP\Methods\Eth\BlockNumber;
use Tourze\Web3PHP\Methods\EthMethod;
use Tourze\Web3PHP\Methods\IMethod;
use Tourze\Web3PHP\Methods\IRPC;

/**
 * BlockNumber 测试
 * @internal
 */
#[CoversClass(BlockNumber::class)]
final class BlockNumberTest extends TestCase
{
    /**
     * 测试构造函数
     */
    public function testConstruct(): void
    {
        $blockNumber = new BlockNumber('eth_blockNumber', []);

        $this->assertInstanceOf(BlockNumber::class, $blockNumber);
        $this->assertSame('eth_blockNumber', (string) $blockNumber);
    }

    /**
     * 测试默认属性值
     */
    public function testDefaultProperties(): void
    {
        $blockNumber = new BlockNumber('eth_blockNumber', []);

        $reflection = new \ReflectionClass($blockNumber);

        // 检查validators属性为空数组
        $validators = $reflection->getProperty('validators');
        $validators->setAccessible(true);
        $this->assertSame([], $validators->getValue($blockNumber));

        // 检查inputFormatters属性为空数组
        $inputFormatters = $reflection->getProperty('inputFormatters');
        $inputFormatters->setAccessible(true);
        $this->assertSame([], $inputFormatters->getValue($blockNumber));

        // 检查outputFormatters属性包含BigNumberFormatter
        $outputFormatters = $reflection->getProperty('outputFormatters');
        $outputFormatters->setAccessible(true);
        $this->assertSame([BigNumberFormatter::class], $outputFormatters->getValue($blockNumber));

        // 检查defaultValues属性为空数组
        $defaultValues = $reflection->getProperty('defaultValues');
        $defaultValues->setAccessible(true);
        $this->assertSame([], $defaultValues->getValue($blockNumber));
    }

    /**
     * 测试toPayload方法
     */
    public function testToPayload(): void
    {
        $blockNumber = new BlockNumber('eth_blockNumber', []);

        $payload = $blockNumber->toPayload();

        $this->assertIsArray($payload);
        $this->assertArrayHasKey('method', $payload);
        $this->assertArrayHasKey('params', $payload);
        $this->assertSame('eth_blockNumber', $payload['method']);
        $this->assertSame([], $payload['params']);
    }

    /**
     * 测试toPayloadString方法
     */
    public function testToPayloadString(): void
    {
        $blockNumber = new BlockNumber('eth_blockNumber', []);

        $payloadString = $blockNumber->toPayloadString();

        $this->assertIsString($payloadString);
        $this->assertStringContainsString('eth_blockNumber', $payloadString);

        // 验证是有效的JSON
        $decoded = json_decode($payloadString, true);
        $this->assertIsArray($decoded);
        $this->assertArrayHasKey('method', $decoded);
        $this->assertArrayHasKey('params', $decoded);
    }

    /**
     * 测试formatOutput方法使用BigNumberFormatter
     */
    public function testFormatOutputUsesBigNumberFormatter(): void
    {
        $blockNumber = new BlockNumber('eth_blockNumber', []);

        // 检查outputFormatters包含BigNumberFormatter
        $reflection = new \ReflectionClass($blockNumber);
        $outputFormatters = $reflection->getProperty('outputFormatters');
        $outputFormatters->setAccessible(true);
        $formatters = $outputFormatters->getValue($blockNumber);

        $this->assertContains(BigNumberFormatter::class, $formatters);

        // 验证formatOutput方法可以正常调用
        // 注意：BigNumberFormatter::format返回BigInteger对象
        $result = $blockNumber->formatOutput('0x123');
        $this->assertInstanceOf(BigInteger::class, $result);
    }

    /**
     * 测试继承关系
     */
    public function testInheritance(): void
    {
        $blockNumber = new BlockNumber('eth_blockNumber', []);

        $this->assertInstanceOf(EthMethod::class, $blockNumber);
        $this->assertInstanceOf(IMethod::class, $blockNumber);
        $this->assertInstanceOf(IRPC::class, $blockNumber);
    }
}
