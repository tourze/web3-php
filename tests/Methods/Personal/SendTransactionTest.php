<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Methods\Personal;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Methods\EthMethod;
use Tourze\Web3PHP\Methods\IMethod;
use Tourze\Web3PHP\Methods\IRPC;
use Tourze\Web3PHP\Methods\Personal\SendTransaction;

/**
 * SendTransaction 测试
 * @internal
 */
#[CoversClass(SendTransaction::class)]
final class SendTransactionTest extends TestCase
{
    /**
     * 测试构造函数
     */
    public function testConstruct(): void
    {
        $sendTransaction = new SendTransaction();

        $this->assertInstanceOf(SendTransaction::class, $sendTransaction);
        $this->assertSame('personal_sendTransaction', (string) $sendTransaction);
    }

    /**
     * 测试默认属性值
     */
    public function testDefaultProperties(): void
    {
        $sendTransaction = new SendTransaction();

        $reflection = new \ReflectionClass($sendTransaction);

        // 检查validators属性
        $validators = $reflection->getProperty('validators');
        $validators->setAccessible(true);
        $this->assertSame([
            'Tourze\Web3PHP\Validators\TransactionValidator',
            'Tourze\Web3PHP\Validators\StringValidator',
        ], $validators->getValue($sendTransaction));

        // 检查inputFormatters属性
        $inputFormatters = $reflection->getProperty('inputFormatters');
        $inputFormatters->setAccessible(true);
        $this->assertSame([
            'Tourze\Web3PHP\Formatters\TransactionFormatter',
            'Tourze\Web3PHP\Formatters\StringFormatter',
        ], $inputFormatters->getValue($sendTransaction));

        // 检查outputFormatters属性为空数组
        $outputFormatters = $reflection->getProperty('outputFormatters');
        $outputFormatters->setAccessible(true);
        $this->assertSame([], $outputFormatters->getValue($sendTransaction));

        // 检查defaultValues属性为空数组
        $defaultValues = $reflection->getProperty('defaultValues');
        $defaultValues->setAccessible(true);
        $this->assertSame([], $defaultValues->getValue($sendTransaction));
    }

    /**
     * 测试toPayload方法
     */
    public function testToPayload(): void
    {
        $sendTransaction = new SendTransaction();

        $payload = $sendTransaction->toPayload();

        $this->assertIsArray($payload);
        $this->assertArrayHasKey('method', $payload);
        $this->assertArrayHasKey('params', $payload);
        $this->assertSame('personal_sendTransaction', $payload['method']);
        $this->assertSame([], $payload['params']);
    }

    /**
     * 测试toPayloadString方法
     */
    public function testToPayloadString(): void
    {
        $sendTransaction = new SendTransaction();

        $payloadString = $sendTransaction->toPayloadString();

        $this->assertIsString($payloadString);
        $this->assertStringContainsString('personal_sendTransaction', $payloadString);

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
        $sendTransaction = new SendTransaction();

        $this->assertInstanceOf(EthMethod::class, $sendTransaction);
        $this->assertInstanceOf(IMethod::class, $sendTransaction);
        $this->assertInstanceOf(IRPC::class, $sendTransaction);
    }
}
