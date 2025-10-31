<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Methods\Personal;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Methods\EthMethod;
use Tourze\Web3PHP\Methods\IMethod;
use Tourze\Web3PHP\Methods\IRPC;
use Tourze\Web3PHP\Methods\Personal\UnlockAccount;

/**
 * UnlockAccount 测试
 * @internal
 */
#[CoversClass(UnlockAccount::class)]
final class UnlockAccountTest extends TestCase
{
    /**
     * 测试构造函数
     */
    public function testConstruct(): void
    {
        $unlockAccount = new UnlockAccount();

        $this->assertInstanceOf(UnlockAccount::class, $unlockAccount);
        $this->assertSame('personal_unlockAccount', (string) $unlockAccount);
    }

    /**
     * 测试默认属性值
     */
    public function testDefaultProperties(): void
    {
        $unlockAccount = new UnlockAccount();

        $reflection = new \ReflectionClass($unlockAccount);

        // 检查validators属性
        $validators = $reflection->getProperty('validators');
        $validators->setAccessible(true);
        $this->assertSame([
            'Tourze\Web3PHP\Validators\AddressValidator',
            'Tourze\Web3PHP\Validators\StringValidator',
            'Tourze\Web3PHP\Validators\QuantityValidator',
        ], $validators->getValue($unlockAccount));

        // 检查inputFormatters属性
        $inputFormatters = $reflection->getProperty('inputFormatters');
        $inputFormatters->setAccessible(true);
        $this->assertSame([
            'Tourze\Web3PHP\Formatters\AddressFormatter',
            'Tourze\Web3PHP\Formatters\StringFormatter',
            'Tourze\Web3PHP\Formatters\NumberFormatter',
        ], $inputFormatters->getValue($unlockAccount));

        // 检查outputFormatters属性为空数组
        $outputFormatters = $reflection->getProperty('outputFormatters');
        $outputFormatters->setAccessible(true);
        $this->assertSame([], $outputFormatters->getValue($unlockAccount));

        // 检查defaultValues属性
        $defaultValues = $reflection->getProperty('defaultValues');
        $defaultValues->setAccessible(true);
        $this->assertSame([
            2 => 300,
        ], $defaultValues->getValue($unlockAccount));
    }

    /**
     * 测试toPayload方法
     */
    public function testToPayload(): void
    {
        $unlockAccount = new UnlockAccount();

        $payload = $unlockAccount->toPayload();

        $this->assertIsArray($payload);
        $this->assertArrayHasKey('method', $payload);
        $this->assertArrayHasKey('params', $payload);
        $this->assertSame('personal_unlockAccount', $payload['method']);
        $this->assertSame([], $payload['params']);
    }

    /**
     * 测试toPayloadString方法
     */
    public function testToPayloadString(): void
    {
        $unlockAccount = new UnlockAccount();

        $payloadString = $unlockAccount->toPayloadString();

        $this->assertIsString($payloadString);
        $this->assertStringContainsString('personal_unlockAccount', $payloadString);

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
        $unlockAccount = new UnlockAccount();

        $this->assertInstanceOf(EthMethod::class, $unlockAccount);
        $this->assertInstanceOf(IMethod::class, $unlockAccount);
        $this->assertInstanceOf(IRPC::class, $unlockAccount);
    }
}
