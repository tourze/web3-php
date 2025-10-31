<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Methods\Web3;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Methods\EthMethod;
use Tourze\Web3PHP\Methods\IMethod;
use Tourze\Web3PHP\Methods\IRPC;
use Tourze\Web3PHP\Methods\Web3\Sha3;

/**
 * Sha3 测试
 * @internal
 */
#[CoversClass(Sha3::class)]
final class Sha3Test extends TestCase
{
    /**
     * 测试构造函数
     */
    public function testConstruct(): void
    {
        $sha3 = new Sha3();

        $this->assertInstanceOf(Sha3::class, $sha3);
        $this->assertSame('web3_sha3', (string) $sha3);
    }

    /**
     * 测试默认属性值
     */
    public function testDefaultProperties(): void
    {
        $sha3 = new Sha3();

        $reflection = new \ReflectionClass($sha3);

        // 检查validators属性
        $validators = $reflection->getProperty('validators');
        $validators->setAccessible(true);
        $this->assertSame([
            'Tourze\Web3PHP\Validators\StringValidator',
        ], $validators->getValue($sha3));

        // 检查inputFormatters属性
        $inputFormatters = $reflection->getProperty('inputFormatters');
        $inputFormatters->setAccessible(true);
        $this->assertSame([
            'Tourze\Web3PHP\Formatters\HexFormatter',
        ], $inputFormatters->getValue($sha3));

        // 检查outputFormatters属性为空数组
        $outputFormatters = $reflection->getProperty('outputFormatters');
        $outputFormatters->setAccessible(true);
        $this->assertSame([], $outputFormatters->getValue($sha3));

        // 检查defaultValues属性为空数组
        $defaultValues = $reflection->getProperty('defaultValues');
        $defaultValues->setAccessible(true);
        $this->assertSame([], $defaultValues->getValue($sha3));
    }

    /**
     * 测试toPayload方法
     */
    public function testToPayload(): void
    {
        $sha3 = new Sha3();

        $payload = $sha3->toPayload();

        $this->assertIsArray($payload);
        $this->assertArrayHasKey('method', $payload);
        $this->assertArrayHasKey('params', $payload);
        $this->assertSame('web3_sha3', $payload['method']);
        $this->assertSame([], $payload['params']);
    }

    /**
     * 测试toPayloadString方法
     */
    public function testToPayloadString(): void
    {
        $sha3 = new Sha3();

        $payloadString = $sha3->toPayloadString();

        $this->assertIsString($payloadString);
        $this->assertStringContainsString('web3_sha3', $payloadString);

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
        $sha3 = new Sha3();

        $this->assertInstanceOf(EthMethod::class, $sha3);
        $this->assertInstanceOf(IMethod::class, $sha3);
        $this->assertInstanceOf(IRPC::class, $sha3);
    }
}
