<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Methods\Personal;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Methods\EthMethod;
use Tourze\Web3PHP\Methods\IMethod;
use Tourze\Web3PHP\Methods\IRPC;
use Tourze\Web3PHP\Methods\Personal\LockAccount;

/**
 * LockAccount 测试
 * @internal
 */
#[CoversClass(LockAccount::class)]
final class LockAccountTest extends TestCase
{
    /**
     * 测试构造函数
     */
    public function testConstruct(): void
    {
        $lockAccount = new LockAccount();

        $this->assertInstanceOf(LockAccount::class, $lockAccount);
        $this->assertSame('personal_lockAccount', (string) $lockAccount);
    }

    /**
     * 测试默认属性值
     */
    public function testDefaultProperties(): void
    {
        $lockAccount = new LockAccount();

        $reflection = new \ReflectionClass($lockAccount);

        // 检查validators属性
        $validators = $reflection->getProperty('validators');
        $validators->setAccessible(true);
        $this->assertSame([
            'Tourze\Web3PHP\Validators\AddressValidator',
        ], $validators->getValue($lockAccount));

        // 检查inputFormatters属性
        $inputFormatters = $reflection->getProperty('inputFormatters');
        $inputFormatters->setAccessible(true);
        $this->assertSame([
            'Tourze\Web3PHP\Formatters\AddressFormatter',
        ], $inputFormatters->getValue($lockAccount));

        // 检查outputFormatters属性为空数组
        $outputFormatters = $reflection->getProperty('outputFormatters');
        $outputFormatters->setAccessible(true);
        $this->assertSame([], $outputFormatters->getValue($lockAccount));

        // 检查defaultValues属性为空数组
        $defaultValues = $reflection->getProperty('defaultValues');
        $defaultValues->setAccessible(true);
        $this->assertSame([], $defaultValues->getValue($lockAccount));
    }

    /**
     * 测试toPayload方法
     */
    public function testToPayload(): void
    {
        $lockAccount = new LockAccount();

        $payload = $lockAccount->toPayload();

        $this->assertIsArray($payload);
        $this->assertArrayHasKey('method', $payload);
        $this->assertArrayHasKey('params', $payload);
        $this->assertSame('personal_lockAccount', $payload['method']);
        $this->assertSame([], $payload['params']);
    }

    /**
     * 测试toPayloadString方法
     */
    public function testToPayloadString(): void
    {
        $lockAccount = new LockAccount();

        $payloadString = $lockAccount->toPayloadString();

        $this->assertIsString($payloadString);
        $this->assertStringContainsString('personal_lockAccount', $payloadString);

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
        $lockAccount = new LockAccount();

        $this->assertInstanceOf(EthMethod::class, $lockAccount);
        $this->assertInstanceOf(IMethod::class, $lockAccount);
        $this->assertInstanceOf(IRPC::class, $lockAccount);
    }
}
