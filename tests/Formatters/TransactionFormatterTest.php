<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Formatters;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Formatters\TransactionFormatter;

/**
 * TransactionFormatter 测试
 * @internal
 */
#[CoversClass(TransactionFormatter::class)]
final class TransactionFormatterTest extends TestCase
{
    /**
     * 测试格式化简单交易
     */
    public function testFormatSimpleTransaction(): void
    {
        $transaction = [
            'from' => '0x1234567890abcdef1234567890abcdef12345678',
            'to' => '0xabcdef1234567890abcdef1234567890abcdef12',
            'value' => '0x0',
        ];

        $result = TransactionFormatter::format($transaction);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('from', $result);
        $this->assertArrayHasKey('to', $result);
    }

    /**
     * 测试格式化完整交易
     */
    public function testFormatFullTransaction(): void
    {
        $transaction = [
            'from' => '0x1234567890abcdef1234567890abcdef12345678',
            'to' => '0xabcdef1234567890abcdef1234567890abcdef12',
            'gas' => '0x5208',
            'gasPrice' => '0x9184e72a000',
            'value' => '0x0',
            'data' => '0x0',
        ];

        $result = TransactionFormatter::format($transaction);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('from', $result);
        $this->assertArrayHasKey('to', $result);
        $this->assertArrayHasKey('gas', $result);
        $this->assertArrayHasKey('gasPrice', $result);
        $this->assertArrayHasKey('value', $result);
        $this->assertArrayHasKey('data', $result);
    }

    /**
     * 测试格式化空交易
     */
    public function testFormatEmptyTransaction(): void
    {
        $transaction = [];

        $result = TransactionFormatter::format($transaction);
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    /**
     * 测试格式化部分交易
     */
    public function testFormatPartialTransaction(): void
    {
        $transaction = [
            'from' => '0x1234567890abcdef1234567890abcdef12345678',
            'value' => '0x1000',
        ];

        $result = TransactionFormatter::format($transaction);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('from', $result);
        $this->assertArrayHasKey('value', $result);
    }
}
