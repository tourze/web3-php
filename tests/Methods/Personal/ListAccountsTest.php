<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Methods\Personal;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Methods\EthMethod;
use Tourze\Web3PHP\Methods\Personal\ListAccounts;

/**
 * ListAccounts 测试
 * @internal
 */
#[CoversClass(ListAccounts::class)]
final class ListAccountsTest extends TestCase
{
    /**
     * 测试构造函数
     */
    public function testConstruct(): void
    {
        $listAccounts = new ListAccounts();

        $this->assertInstanceOf(ListAccounts::class, $listAccounts);
        $this->assertInstanceOf(EthMethod::class, $listAccounts);
        $this->assertSame('personal_listAccounts', (string) $listAccounts);
    }

    /**
     * 测试默认属性值
     */
    public function testDefaultProperties(): void
    {
        $listAccounts = new ListAccounts();

        $reflection = new \ReflectionClass($listAccounts);

        // 检查validators属性为空数组
        $validators = $reflection->getProperty('validators');
        $validators->setAccessible(true);
        $this->assertSame([], $validators->getValue($listAccounts));

        // 检查inputFormatters属性为空数组
        $inputFormatters = $reflection->getProperty('inputFormatters');
        $inputFormatters->setAccessible(true);
        $this->assertSame([], $inputFormatters->getValue($listAccounts));

        // 检查outputFormatters属性为空数组
        $outputFormatters = $reflection->getProperty('outputFormatters');
        $outputFormatters->setAccessible(true);
        $this->assertSame([], $outputFormatters->getValue($listAccounts));

        // 检查defaultValues属性为空数组
        $defaultValues = $reflection->getProperty('defaultValues');
        $defaultValues->setAccessible(true);
        $this->assertSame([], $defaultValues->getValue($listAccounts));
    }

    /**
     * 测试toPayload方法
     */
    public function testToPayload(): void
    {
        $listAccounts = new ListAccounts();

        $payload = $listAccounts->toPayload();

        $this->assertIsArray($payload);
        $this->assertArrayHasKey('method', $payload);
        $this->assertArrayHasKey('params', $payload);
        $this->assertSame('personal_listAccounts', $payload['method']);
        $this->assertSame([], $payload['params']);
    }

    /**
     * 测试toPayloadString方法
     */
    public function testToPayloadString(): void
    {
        $listAccounts = new ListAccounts();

        $payloadString = $listAccounts->toPayloadString();

        $this->assertIsString($payloadString);
        $this->assertStringContainsString('personal_listAccounts', $payloadString);

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
        $listAccounts = new ListAccounts();

        $this->assertInstanceOf(EthMethod::class, $listAccounts);
    }
}
