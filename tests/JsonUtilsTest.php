<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Exception\InvalidArgumentException;
use Tourze\Web3PHP\JsonUtils;

/**
 * @internal
 */
#[CoversClass(JsonUtils::class)]
final class JsonUtilsTest extends TestCase
{
    public function testJsonMethodToStringWithStdClass(): void
    {
        $json = new \stdClass();
        $json->name = 'transfer';
        $json->inputs = [
            (object) ['type' => 'address'],
            (object) ['type' => 'uint256'],
        ];

        $result = JsonUtils::jsonMethodToString($json);
        $this->assertSame('transfer(address,uint256)', $result);
    }

    public function testJsonMethodToStringWithArray(): void
    {
        $json = [
            'name' => 'approve',
            'inputs' => [
                ['type' => 'address'],
                ['type' => 'uint256'],
            ],
        ];

        $result = JsonUtils::jsonMethodToString($json);
        $this->assertSame('approve(address,uint256)', $result);
    }

    public function testJsonMethodToStringWithPreformattedName(): void
    {
        $json = [
            'name' => 'transfer(address,uint256)',
            'inputs' => [],
        ];

        $result = JsonUtils::jsonMethodToString($json);
        $this->assertSame('transfer(address,uint256)', $result);
    }

    public function testJsonMethodToStringThrowsExceptionForInvalidType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('jsonMethodToString json must be array or stdClass.');
        JsonUtils::jsonMethodToString('invalid');
    }

    public function testJsonToArrayWithStdClass(): void
    {
        $json = new \stdClass();
        $json->name = 'test';
        $json->nested = new \stdClass();
        $json->nested->value = 'nested_value';

        $result = JsonUtils::jsonToArray($json);
        $this->assertIsArray($result);
        $this->assertSame('test', $result['name']);
        $this->assertIsArray($result['nested']);
        $this->assertSame('nested_value', $result['nested']['value']);
    }

    public function testJsonToArrayWithArray(): void
    {
        $json = [
            'name' => 'test',
            'nested' => (object) ['value' => 'nested_value'],
            'items' => [
                (object) ['item' => 'item1'],
            ],
        ];

        $result = JsonUtils::jsonToArray($json);
        $this->assertIsArray($result);
        $this->assertSame('test', $result['name']);
        $this->assertIsArray($result['nested']);
        $this->assertSame('nested_value', $result['nested']['value']);
        $this->assertIsArray($result['items'][0]);
        $this->assertSame('item1', $result['items'][0]['item']);
    }
}
