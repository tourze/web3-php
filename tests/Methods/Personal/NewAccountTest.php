<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Methods\Personal;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Methods\EthMethod;
use Tourze\Web3PHP\Methods\IMethod;
use Tourze\Web3PHP\Methods\IRPC;
use Tourze\Web3PHP\Methods\Personal\NewAccount;

/**
 * NewAccount 测试
 * @internal
 */
#[CoversClass(NewAccount::class)]
final class NewAccountTest extends TestCase
{
    /**
     * 测试构造函数
     */
    public function testConstruct(): void
    {
        $newAccount = new NewAccount();

        $this->assertInstanceOf(NewAccount::class, $newAccount);
        $this->assertSame('personal_newAccount', (string) $newAccount);
    }

    /**
     * 测试默认属性值
     */
    public function testDefaultProperties(): void
    {
        $newAccount = new NewAccount();

        $reflection = new \ReflectionClass($newAccount);

        // 检查validators属性
        $validators = $reflection->getProperty('validators');
        $validators->setAccessible(true);
        $this->assertSame([
            'Tourze\Web3PHP\Validators\StringValidator',
        ], $validators->getValue($newAccount));

        // 检查inputFormatters属性
        $inputFormatters = $reflection->getProperty('inputFormatters');
        $inputFormatters->setAccessible(true);
        $this->assertSame([
            'Tourze\Web3PHP\Formatters\StringFormatter',
        ], $inputFormatters->getValue($newAccount));

        // 检查outputFormatters属性为空数组
        $outputFormatters = $reflection->getProperty('outputFormatters');
        $outputFormatters->setAccessible(true);
        $this->assertSame([], $outputFormatters->getValue($newAccount));

        // 检查defaultValues属性为空数组
        $defaultValues = $reflection->getProperty('defaultValues');
        $defaultValues->setAccessible(true);
        $this->assertSame([], $defaultValues->getValue($newAccount));
    }

    /**
     * 测试toPayload方法
     */
    public function testToPayload(): void
    {
        $newAccount = new NewAccount();

        $payload = $newAccount->toPayload();

        $this->assertIsArray($payload);
        $this->assertArrayHasKey('method', $payload);
        $this->assertArrayHasKey('params', $payload);
        $this->assertSame('personal_newAccount', $payload['method']);
        $this->assertSame([], $payload['params']);
    }

    /**
     * 测试toPayloadString方法
     */
    public function testToPayloadString(): void
    {
        $newAccount = new NewAccount();

        $payloadString = $newAccount->toPayloadString();

        $this->assertIsString($payloadString);
        $this->assertStringContainsString('personal_newAccount', $payloadString);

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
        $newAccount = new NewAccount();

        $this->assertInstanceOf(EthMethod::class, $newAccount);
        $this->assertInstanceOf(IMethod::class, $newAccount);
        $this->assertInstanceOf(IRPC::class, $newAccount);
    }
}
