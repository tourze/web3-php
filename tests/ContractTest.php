<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Contract;
use Tourze\Web3PHP\Contracts\Ethabi;
use Tourze\Web3PHP\Exception\InvalidArgumentException;

/**
 * @internal
 */
#[CoversClass(Contract::class)]
final class ContractTest extends TestCase
{
    private Contract $contract;

    /** @var array<int, array<string, mixed>> */
    private array $testAbi;

    protected function setUp(): void
    {
        parent::setUp();

        $this->testAbi = [
            [
                'type' => 'function',
                'name' => 'balanceOf',
                'inputs' => [
                    ['name' => '_owner', 'type' => 'address'],
                ],
                'outputs' => [
                    ['name' => 'balance', 'type' => 'uint256'],
                ],
            ],
            [
                'type' => 'function',
                'name' => 'transfer',
                'inputs' => [
                    ['name' => '_to', 'type' => 'address'],
                    ['name' => '_value', 'type' => 'uint256'],
                ],
                'outputs' => [
                    ['name' => '', 'type' => 'bool'],
                ],
            ],
            [
                'type' => 'constructor',
                'inputs' => [
                    ['name' => '_initialSupply', 'type' => 'uint256'],
                ],
                'outputs' => [],
            ],
            [
                'type' => 'event',
                'name' => 'Transfer',
                'inputs' => [
                    ['indexed' => true, 'name' => '_from', 'type' => 'address'],
                    ['indexed' => true, 'name' => '_to', 'type' => 'address'],
                    ['indexed' => false, 'name' => '_value', 'type' => 'uint256'],
                ],
            ],
        ];

        $this->contract = new Contract('http://localhost:8545', $this->testAbi);
    }

    public function testConstructor(): void
    {
        $this->assertInstanceOf(Contract::class, $this->contract);
    }

    public function testGetAbi(): void
    {
        $abi = $this->contract->getAbi();
        $this->assertIsArray($abi);
        $this->assertCount(4, $abi);
    }

    public function testGetFunctions(): void
    {
        $functions = $this->contract->getFunctions();
        $this->assertIsArray($functions);
        $this->assertArrayHasKey('balanceOf', $functions);
        $this->assertArrayHasKey('transfer', $functions);
    }

    public function testGetEvents(): void
    {
        $events = $this->contract->getEvents();
        $this->assertIsArray($events);
        $this->assertArrayHasKey('Transfer', $events);
    }

    public function testGetConstructor(): void
    {
        $constructor = $this->contract->getConstructor();
        $this->assertIsArray($constructor);
        $this->assertEquals('constructor', $constructor['type']);
    }

    public function testGetEthabi(): void
    {
        $ethabi = $this->contract->getEthabi();
        $this->assertInstanceOf(Ethabi::class, $ethabi);
    }

    public function testSetBytecode(): void
    {
        $bytecode = '0x60806040';
        $this->contract->setBytecode($bytecode);
        $this->assertEquals($bytecode, $this->contract->getBytecode());
    }

    public function testSetToAddress(): void
    {
        $address = '0x742d35Cc6634C0532925a3b844Bc9e7595f0bEb3';
        $expectedAddress = '0x742d35cc6634c0532925a3b844bc9e7595f0beb3'; // 期望的小写格式
        $this->contract->setToAddress($address);
        $this->assertEquals($expectedAddress, $this->contract->getToAddress());
    }

    public function testGetEth(): void
    {
        $eth = $this->contract->getEth();
        $this->assertNotNull($eth);
    }

    public function testSetDefaultBlock(): void
    {
        $block = 'latest';
        $this->contract->setDefaultBlock($block);
        $this->assertEquals($block, $this->contract->getDefaultBlock());
    }

    public function testAt(): void
    {
        $address = '0x742d35Cc6634C0532925a3b844Bc9e7595f0bEb3';
        $this->contract->at($address);
        $this->assertEquals('0x742d35cc6634c0532925a3b844bc9e7595f0beb3', $this->contract->getToAddress());
    }

    public function testBytecode(): void
    {
        $bytecode = '0x60806040';
        $this->contract->bytecode($bytecode);
        $this->assertEquals($bytecode, $this->contract->getBytecode());
    }

    public function testAbi(): void
    {
        $newAbi = '[{"type": "function", "name": "test", "inputs": [], "outputs": []}]';
        $expectedAbi = [['type' => 'function', 'name' => 'test', 'inputs' => [], 'outputs' => []]];
        $this->contract->abi($newAbi);
        $this->assertEquals($expectedAbi, $this->contract->getAbi());
    }

    public function testNew(): void
    {
        $this->contract->setBytecode('0x60806040');

        // 测试 new 方法需要构造函数参数和回调函数
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Please make sure you have put all constructor params and callback.');
        $this->contract->new();
    }

    public function testSend(): void
    {
        $this->contract->setToAddress('0x742d35Cc6634C0532925a3b844Bc9e7595f0bEb3');

        // 测试 send 方法需要方法名参数
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Please make sure the method is string.');
        $this->contract->send();
    }

    public function testCall(): void
    {
        $this->contract->setToAddress('0x742d35Cc6634C0532925a3b844Bc9e7595f0bEb3');

        // 测试 call 方法需要方法名参数
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Please make sure the method is string.');
        $this->contract->call();
    }

    public function testEstimateGas(): void
    {
        $this->contract->setToAddress('0x742d35Cc6634C0532925a3b844Bc9e7595f0bEb3');

        // 测试 estimateGas 方法需要方法名参数
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Please make sure the method is string.');
        $this->contract->estimateGas();
    }
}
