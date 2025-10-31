<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Contract;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Contract\AbiParser;
use Tourze\Web3PHP\Exception\InvalidArgumentException;

/**
 * AbiParser 测试
 * @internal
 */
#[CoversClass(AbiParser::class)]
final class AbiParserTest extends TestCase
{
    private AbiParser $abiParser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->abiParser = new AbiParser();
    }

    /**
     * 测试解析包含函数的JSON字符串ABI
     */
    public function testParseJsonStringAbiWithFunctionsShouldReturnParsedStructure(): void
    {
        $abiJson = '[
            {
                "type": "function",
                "name": "transfer",
                "inputs": [
                    {"type": "address", "name": "to"},
                    {"type": "uint256", "name": "amount"}
                ],
                "outputs": [
                    {"type": "bool", "name": "success"}
                ]
            },
            {
                "type": "constructor",
                "inputs": [
                    {"type": "string", "name": "name"},
                    {"type": "string", "name": "symbol"}
                ]
            },
            {
                "type": "event",
                "name": "Transfer",
                "inputs": [
                    {"type": "address", "name": "from", "indexed": true},
                    {"type": "address", "name": "to", "indexed": true},
                    {"type": "uint256", "name": "value"}
                ]
            }
        ]';

        $result = $this->abiParser->parse($abiJson);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('functions', $result);
        $this->assertArrayHasKey('constructor', $result);
        $this->assertArrayHasKey('events', $result);
        $this->assertArrayHasKey('abi', $result);
        $this->assertArrayHasKey('transfer', $result['functions']);
        $this->assertEquals('function', $result['functions']['transfer']['type']);
        $this->assertArrayHasKey('Transfer', $result['events']);
        $this->assertEquals('constructor', $result['constructor']['type']);
    }

    /**
     * 测试解析数组格式ABI
     */
    public function testParseArrayAbiWithMultipleFunctionsShouldReturnParsedStructure(): void
    {
        $abiArray = [
            [
                'type' => 'function',
                'name' => 'approve',
                'inputs' => [
                    ['type' => 'address', 'name' => 'spender'],
                    ['type' => 'uint256', 'name' => 'amount'],
                ],
            ],
            [
                'type' => 'function',
                'name' => 'balanceOf',
                'inputs' => [
                    ['type' => 'address', 'name' => 'owner'],
                ],
                'outputs' => [
                    ['type' => 'uint256', 'name' => 'balance'],
                ],
            ],
        ];

        $result = $this->abiParser->parse($abiArray);

        $this->assertCount(2, $result['functions']);
        $this->assertArrayHasKey('approve', $result['functions']);
        $this->assertArrayHasKey('balanceOf', $result['functions']);
        $this->assertEmpty($result['constructor']);
        $this->assertEmpty($result['events']);
    }

    /**
     * 测试解析stdClass对象ABI
     */
    public function testParseStdClassAbiShouldReturnParsedStructure(): void
    {
        $abiObject = new \stdClass();
        $abiObject->type = 'function';
        $abiObject->name = 'totalSupply';
        $abiObject->inputs = [];
        $abiObject->outputs = [['type' => 'uint256', 'name' => 'supply']];

        // 由于Utils::jsonToArray会处理stdClass，我们需要构造一个包含数组的结构
        $abiData = [$abiObject];

        $result = $this->abiParser->parse($abiData);

        $this->assertArrayHasKey('totalSupply', $result['functions']);
        $this->assertEquals('function', $result['functions']['totalSupply']['type']);
        $this->assertEmpty($result['constructor']);
        $this->assertEmpty($result['events']);
    }

    /**
     * 测试解析空ABI
     */
    public function testParseEmptyAbiShouldReturnEmptyStructure(): void
    {
        $result = $this->abiParser->parse([]);

        $this->assertEmpty($result['functions']);
        $this->assertEmpty($result['constructor']);
        $this->assertEmpty($result['events']);
        $this->assertEmpty($result['abi']);
    }

    /**
     * 测试解析只包含事件的ABI
     */
    public function testParseAbiWithOnlyEventsShouldReturnEventsOnly(): void
    {
        $abiArray = [
            [
                'type' => 'event',
                'name' => 'Approval',
                'inputs' => [
                    ['type' => 'address', 'name' => 'owner', 'indexed' => true],
                    ['type' => 'address', 'name' => 'spender', 'indexed' => true],
                    ['type' => 'uint256', 'name' => 'value'],
                ],
            ],
        ];

        $result = $this->abiParser->parse($abiArray);

        $this->assertEmpty($result['functions']);
        $this->assertEmpty($result['constructor']);
        $this->assertCount(1, $result['events']);
        $this->assertArrayHasKey('Approval', $result['events']);
    }

    /**
     * 测试解析只包含构造函数的ABI
     */
    public function testParseAbiWithOnlyConstructorShouldReturnConstructorOnly(): void
    {
        $abiArray = [
            [
                'type' => 'constructor',
                'inputs' => [
                    ['type' => 'uint256', 'name' => '_initialSupply'],
                ],
            ],
        ];

        $result = $this->abiParser->parse($abiArray);

        $this->assertEmpty($result['functions']);
        $this->assertNotEmpty($result['constructor']);
        $this->assertEquals('constructor', $result['constructor']['type']);
        $this->assertEmpty($result['events']);
    }

    /**
     * 测试解析包含多个同名函数的ABI（函数重载）
     */
    public function testParseAbiWithOverloadedFunctionsShouldKeepLastOne(): void
    {
        $abiArray = [
            [
                'type' => 'function',
                'name' => 'transfer',
                'inputs' => [
                    ['type' => 'address', 'name' => 'to'],
                ],
            ],
            [
                'type' => 'function',
                'name' => 'transfer',
                'inputs' => [
                    ['type' => 'address', 'name' => 'to'],
                    ['type' => 'uint256', 'name' => 'amount'],
                ],
            ],
        ];

        $result = $this->abiParser->parse($abiArray);

        $this->assertCount(1, $result['functions']);
        $this->assertArrayHasKey('transfer', $result['functions']);
        // 应该保留最后一个定义
        $this->assertCount(2, $result['functions']['transfer']['inputs']);
    }

    /**
     * 测试解析包含无效type的ABI项目
     */
    public function testParseAbiWithInvalidTypeShouldIgnoreInvalidItems(): void
    {
        $abiArray = [
            [
                'type' => 'function',
                'name' => 'validFunction',
                'inputs' => [],
            ],
            [
                'type' => 'invalid_type',
                'name' => 'invalidItem',
                'inputs' => [],
            ],
            [
                'type' => 'event',
                'name' => 'ValidEvent',
                'inputs' => [],
            ],
        ];

        $result = $this->abiParser->parse($abiArray);

        $this->assertCount(1, $result['functions']);
        $this->assertCount(1, $result['events']);
        $this->assertArrayHasKey('validFunction', $result['functions']);
        $this->assertArrayHasKey('ValidEvent', $result['events']);
        $this->assertArrayNotHasKey('invalidItem', $result['functions']);
    }

    /**
     * 测试解析缺少name字段的函数/事件
     */
    public function testParseAbiWithMissingNameShouldIgnoreItems(): void
    {
        $abiArray = [
            [
                'type' => 'function',
                // 缺少 name 字段
                'inputs' => [],
            ],
            [
                'type' => 'event',
                // 缺少 name 字段
                'inputs' => [],
            ],
            [
                'type' => 'constructor',
                'inputs' => [],
            ],
        ];

        $result = $this->abiParser->parse($abiArray);

        $this->assertEmpty($result['functions']);
        $this->assertEmpty($result['events']);
        $this->assertNotEmpty($result['constructor']); // 构造函数不需要name字段
    }

    /**
     * 测试解析无效JSON格式
     */
    public function testParseInvalidJsonStringShouldThrowException(): void
    {
        $invalidJson = '{"type": "function", "name": "test"'; // 缺少闭合括号

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/abi decode error:/');

        $this->abiParser->parse($invalidJson);
    }

    /**
     * 测试解析复杂的真实合约ABI
     */
    public function testParseComplexRealWorldAbiShouldReturnCorrectStructure(): void
    {
        $complexAbi = [
            [
                'type' => 'constructor',
                'inputs' => [
                    ['type' => 'string', 'name' => 'name'],
                    ['type' => 'string', 'name' => 'symbol'],
                    ['type' => 'uint8', 'name' => 'decimals'],
                ],
            ],
            [
                'type' => 'function',
                'name' => 'transfer',
                'inputs' => [
                    ['type' => 'address', 'name' => 'to'],
                    ['type' => 'uint256', 'name' => 'amount'],
                ],
                'outputs' => [
                    ['type' => 'bool', 'name' => 'success'],
                ],
                'stateMutability' => 'nonpayable',
            ],
            [
                'type' => 'function',
                'name' => 'balanceOf',
                'inputs' => [
                    ['type' => 'address', 'name' => 'account'],
                ],
                'outputs' => [
                    ['type' => 'uint256', 'name' => 'balance'],
                ],
                'stateMutability' => 'view',
            ],
            [
                'type' => 'event',
                'name' => 'Transfer',
                'inputs' => [
                    ['type' => 'address', 'name' => 'from', 'indexed' => true],
                    ['type' => 'address', 'name' => 'to', 'indexed' => true],
                    ['type' => 'uint256', 'name' => 'value', 'indexed' => false],
                ],
            ],
        ];

        $result = $this->abiParser->parse($complexAbi);

        $this->assertCount(2, $result['functions']);
        $this->assertCount(1, $result['events']);
        $this->assertEquals('constructor', $result['constructor']['type']);
        $this->assertArrayHasKey('transfer', $result['functions']);
        $this->assertArrayHasKey('balanceOf', $result['functions']);
        $this->assertArrayHasKey('Transfer', $result['events']);

        // 检查函数的stateMutability属性被保留
        $this->assertEquals('nonpayable', $result['functions']['transfer']['stateMutability']);
        $this->assertEquals('view', $result['functions']['balanceOf']['stateMutability']);
    }

    /**
     * 测试解析包含多个构造函数的ABI（应该只保留第一个）
     */
    public function testParseAbiWithMultipleConstructorsShouldKeepFirstOne(): void
    {
        $abiArray = [
            [
                'type' => 'constructor',
                'inputs' => [
                    ['type' => 'string', 'name' => 'name'],
                ],
            ],
            [
                'type' => 'constructor',
                'inputs' => [
                    ['type' => 'string', 'name' => 'name'],
                    ['type' => 'string', 'name' => 'symbol'],
                ],
            ],
        ];

        $result = $this->abiParser->parse($abiArray);

        $this->assertNotEmpty($result['constructor']);
        // 实际上保留第一个构造函数定义（由于foreach的return行为）
        $this->assertCount(1, $result['constructor']['inputs']);
        $this->assertEquals('name', $result['constructor']['inputs'][0]['name']);
    }

    /**
     * 测试解析包含多个同名事件的ABI
     */
    public function testParseAbiWithMultipleSameNameEventsShouldKeepLastOne(): void
    {
        $abiArray = [
            [
                'type' => 'event',
                'name' => 'TestEvent',
                'inputs' => [
                    ['type' => 'uint256', 'name' => 'value'],
                ],
            ],
            [
                'type' => 'event',
                'name' => 'TestEvent',
                'inputs' => [
                    ['type' => 'uint256', 'name' => 'value'],
                    ['type' => 'address', 'name' => 'sender'],
                ],
            ],
        ];

        $result = $this->abiParser->parse($abiArray);

        $this->assertCount(1, $result['events']);
        $this->assertArrayHasKey('TestEvent', $result['events']);
        // 应该保留最后一个事件定义
        $this->assertCount(2, $result['events']['TestEvent']['inputs']);
    }
}
