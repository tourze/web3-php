<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Methods;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Methods\JsonRpc;

/**
 * JsonRpc 测试
 * @internal
 */
#[CoversClass(JsonRpc::class)]
final class JsonRpcTest extends TestCase
{
    /**
     * 测试构造函数
     */
    public function testConstruct(): void
    {
        $jsonRpc = new JsonRpc('testMethod', ['arg1', 'arg2'], 123);

        $this->assertSame('testMethod', $jsonRpc->getMethod());
        $this->assertSame(['arg1', 'arg2'], $jsonRpc->getParams());
        $this->assertSame(123, $jsonRpc->getId());
        $this->assertSame('2.0', $jsonRpc->getJsonRpc());
    }

    /**
     * 测试默认构造函数值
     */
    public function testConstructWithDefaults(): void
    {
        $jsonRpc = new JsonRpc();

        $this->assertSame('', $jsonRpc->getMethod());
        $this->assertSame([], $jsonRpc->getParams());
        $this->assertSame(1, $jsonRpc->getId());
        $this->assertSame('2.0', $jsonRpc->getJsonRpc());
    }

    /**
     * 测试ID的getter和setter
     */
    public function testIdGetterAndSetter(): void
    {
        $jsonRpc = new JsonRpc();

        $this->assertSame(1, $jsonRpc->getId());

        $jsonRpc->setId(456);
        $this->assertSame(456, $jsonRpc->getId());
    }

    /**
     * 测试JsonRpc版本的getter和setter
     */
    public function testJsonRpcGetterAndSetter(): void
    {
        $jsonRpc = new JsonRpc();

        $this->assertSame('2.0', $jsonRpc->getJsonRpc());

        $jsonRpc->setJsonRpc('1.0');
        $this->assertSame('1.0', $jsonRpc->getJsonRpc());
    }

    /**
     * 测试方法的getter和setter
     */
    public function testMethodGetterAndSetter(): void
    {
        $jsonRpc = new JsonRpc();

        $this->assertSame('', $jsonRpc->getMethod());

        $jsonRpc->setMethod('eth_getBalance');
        $this->assertSame('eth_getBalance', $jsonRpc->getMethod());
    }

    /**
     * 测试参数的getter和setter
     */
    public function testParamsGetterAndSetter(): void
    {
        $jsonRpc = new JsonRpc();

        $this->assertSame([], $jsonRpc->getParams());

        $params = ['0x123', 'latest'];
        $jsonRpc->setParams($params);
        $this->assertSame($params, $jsonRpc->getParams());
    }

    /**
     * 测试toArray方法
     */
    public function testToArray(): void
    {
        $jsonRpc = new JsonRpc('eth_getBalance', ['0x123', 'latest'], 789);

        $expected = [
            'id' => 789,
            'jsonrpc' => '2.0',
            'method' => 'eth_getBalance',
            'params' => ['0x123', 'latest'],
        ];

        $this->assertSame($expected, $jsonRpc->toArray());
    }

    /**
     * 测试__toString方法
     */
    public function testToString(): void
    {
        $jsonRpc = new JsonRpc('eth_getBalance', ['0x123', 'latest'], 789);

        $stringResult = (string) $jsonRpc;

        $this->assertIsString($stringResult);
        $this->assertStringContainsString('"id":789', $stringResult);
        $this->assertStringContainsString('"jsonrpc":"2.0"', $stringResult);
        $this->assertStringContainsString('"method":"eth_getBalance"', $stringResult);
        $this->assertStringContainsString('"params":["0x123","latest"]', $stringResult);
    }

    /**
     * 测试空参数的toArray方法
     */
    public function testToArrayWithEmptyParams(): void
    {
        $jsonRpc = new JsonRpc('eth_blockNumber', [], 1);

        $expected = [
            'id' => 1,
            'jsonrpc' => '2.0',
            'method' => 'eth_blockNumber',
            'params' => [],
        ];

        $this->assertSame($expected, $jsonRpc->toArray());
    }

    /**
     * 测试复杂参数的toArray方法
     */
    public function testToArrayWithComplexParams(): void
    {
        $complexParams = [
            'from' => '0x123',
            'to' => '0x456',
            'value' => '0x0',
            'gas' => '0x5208',
        ];

        $jsonRpc = new JsonRpc('eth_sendTransaction', $complexParams, 42);

        $expected = [
            'id' => 42,
            'jsonrpc' => '2.0',
            'method' => 'eth_sendTransaction',
            'params' => $complexParams,
        ];

        $this->assertSame($expected, $jsonRpc->toArray());
    }
}
