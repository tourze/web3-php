<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Methods\Eth;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Methods\Eth\Accounts;
use Tourze\Web3PHP\Methods\EthMethod;
use Tourze\Web3PHP\Methods\IMethod;
use Tourze\Web3PHP\Methods\IRPC;

/**
 * Accounts 测试
 * @internal
 */
#[CoversClass(Accounts::class)]
final class AccountsTest extends TestCase
{
    /**
     * 测试构造函数
     */
    public function testConstruct(): void
    {
        $accounts = new Accounts();

        $this->assertInstanceOf(Accounts::class, $accounts);
        $this->assertSame('eth_accounts', (string) $accounts);
    }

    /**
     * 测试默认属性值
     */
    public function testDefaultProperties(): void
    {
        $accounts = new Accounts();

        $reflection = new \ReflectionClass($accounts);

        // 检查validators属性为空数组
        $validators = $reflection->getProperty('validators');
        $validators->setAccessible(true);
        $this->assertSame([], $validators->getValue($accounts));

        // 检查inputFormatters属性为空数组
        $inputFormatters = $reflection->getProperty('inputFormatters');
        $inputFormatters->setAccessible(true);
        $this->assertSame([], $inputFormatters->getValue($accounts));

        // 检查outputFormatters属性为空数组
        $outputFormatters = $reflection->getProperty('outputFormatters');
        $outputFormatters->setAccessible(true);
        $this->assertSame([], $outputFormatters->getValue($accounts));

        // 检查defaultValues属性为空数组
        $defaultValues = $reflection->getProperty('defaultValues');
        $defaultValues->setAccessible(true);
        $this->assertSame([], $defaultValues->getValue($accounts));
    }

    /**
     * 测试toPayload方法
     */
    public function testToPayload(): void
    {
        $accounts = new Accounts();

        $payload = $accounts->toPayload();

        $this->assertIsArray($payload);
        $this->assertArrayHasKey('method', $payload);
        $this->assertArrayHasKey('params', $payload);
        $this->assertSame('eth_accounts', $payload['method']);
        $this->assertSame([], $payload['params']);
    }

    /**
     * 测试toPayloadString方法
     */
    public function testToPayloadString(): void
    {
        $accounts = new Accounts();

        $payloadString = $accounts->toPayloadString();

        $this->assertIsString($payloadString);
        $this->assertStringContainsString('eth_accounts', $payloadString);

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
        $accounts = new Accounts();

        $this->assertInstanceOf(EthMethod::class, $accounts);
        $this->assertInstanceOf(IMethod::class, $accounts);
        $this->assertInstanceOf(IRPC::class, $accounts);
    }
}
