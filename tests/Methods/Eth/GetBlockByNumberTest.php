<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Methods\Eth;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Formatters\BooleanFormatter;
use Tourze\Web3PHP\Formatters\OptionalQuantityFormatter;
use Tourze\Web3PHP\Methods\Eth\GetBlockByNumber;
use Tourze\Web3PHP\Methods\EthMethod;
use Tourze\Web3PHP\Methods\IMethod;
use Tourze\Web3PHP\Methods\IRPC;
use Tourze\Web3PHP\Validators\BooleanValidator;
use Tourze\Web3PHP\Validators\QuantityValidator;
use Tourze\Web3PHP\Validators\TagValidator;

/**
 * GetBlockByNumber 测试
 * @internal
 */
#[CoversClass(GetBlockByNumber::class)]
final class GetBlockByNumberTest extends TestCase
{
    /**
     * 测试构造函数
     */
    public function testConstruct(): void
    {
        $method = new GetBlockByNumber();

        $this->assertInstanceOf(GetBlockByNumber::class, $method);
        $this->assertSame('eth_getBlockByNumber', (string) $method);
    }

    /**
     * 测试默认属性值
     */
    public function testDefaultProperties(): void
    {
        $method = new GetBlockByNumber();

        $reflection = new \ReflectionClass($method);

        // 检查validators属性
        $validators = $reflection->getProperty('validators');
        $validators->setAccessible(true);
        $expectedValidators = [
            [
                QuantityValidator::class,
                TagValidator::class,
            ],
            BooleanValidator::class,
        ];
        $this->assertSame($expectedValidators, $validators->getValue($method));

        // 检查inputFormatters属性
        $inputFormatters = $reflection->getProperty('inputFormatters');
        $inputFormatters->setAccessible(true);
        $expectedInputFormatters = [
            OptionalQuantityFormatter::class,
            BooleanFormatter::class,
        ];
        $this->assertSame($expectedInputFormatters, $inputFormatters->getValue($method));

        // 检查outputFormatters属性为空数组
        $outputFormatters = $reflection->getProperty('outputFormatters');
        $outputFormatters->setAccessible(true);
        $this->assertSame([], $outputFormatters->getValue($method));

        // 检查defaultValues属性
        $defaultValues = $reflection->getProperty('defaultValues');
        $defaultValues->setAccessible(true);
        $this->assertSame([0 => 'latest'], $defaultValues->getValue($method));
    }

    /**
     * 测试toPayload方法
     */
    public function testToPayload(): void
    {
        $method = new GetBlockByNumber();

        $payload = $method->toPayload();

        $this->assertIsArray($payload);
        $this->assertArrayHasKey('method', $payload);
        $this->assertArrayHasKey('params', $payload);
        $this->assertSame('eth_getBlockByNumber', $payload['method']);
        $this->assertSame([], $payload['params']);
    }

    /**
     * 测试toPayloadString方法
     */
    public function testToPayloadString(): void
    {
        $method = new GetBlockByNumber();

        $payloadString = $method->toPayloadString();

        $this->assertIsString($payloadString);
        $this->assertStringContainsString('eth_getBlockByNumber', $payloadString);

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
        $method = new GetBlockByNumber();

        $this->assertInstanceOf(EthMethod::class, $method);
        $this->assertInstanceOf(IMethod::class, $method);
        $this->assertInstanceOf(IRPC::class, $method);
    }
}
