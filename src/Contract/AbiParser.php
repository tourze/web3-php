<?php

/**
 * 此文件是 web3.php 包的一部分。
 *
 * (c) Kuan-Cheng,Lai <alk03073135@gmail.com>
 *
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */

namespace Tourze\Web3PHP\Contract;

use Tourze\Web3PHP\Exception\InvalidArgumentException;
use Tourze\Web3PHP\Utils;

/**
 * ABI解析器 - 专门处理ABI相关逻辑
 */
class AbiParser
{
    /**
     * 解析ABI定义
     *
     * @param string|\stdClass|array<string, mixed>|array<int, array<string, mixed>> $abi
     * @return array{functions: array<string, array<string, mixed>>, constructor: array<string, mixed>, events: array<string, array<string, mixed>>, abi: array<string, mixed>}
     */
    public function parse($abi): array
    {
        $abiArray = $this->parseAbiInput($abi);

        return [
            'functions' => $this->extractFunctions($abiArray),
            'constructor' => $this->extractConstructor($abiArray),
            'events' => $this->extractEvents($abiArray),
            'abi' => $abiArray,
        ];
    }

    /**
     * 解析ABI输入
     *
     * @param mixed $abi
     * @return array<string, mixed>
     */
    private function parseAbiInput($abi): array
    {
        if (is_string($abi)) {
            return $this->decodeJsonAbi($abi);
        }

        return Utils::jsonToArray($abi);
    }

    /**
     * 解码JSON格式ABI
     *
     * @param string $abi
     * @return array<string, mixed>
     */
    private function decodeJsonAbi(string $abi): array
    {
        $decoded = json_decode($abi, true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new InvalidArgumentException('abi decode error: ' . json_last_error_msg());
        }

        return $decoded;
    }

    /**
     * 提取函数定义
     *
     * @param array<string, mixed> $abiArray
     * @return array<string, array<string, mixed>>
     */
    private function extractFunctions(array $abiArray): array
    {
        $functions = [];

        foreach ($abiArray as $item) {
            if (!is_array($item)) {
                continue;
            }
            if (isset($item['type'], $item['name']) && 'function' === $item['type']) {
                $functions[$item['name']] = $item;
            }
        }

        return $functions;
    }

    /**
     * 提取构造函数定义
     *
     * @param array<string, mixed> $abiArray
     * @return array<string, mixed>
     */
    private function extractConstructor(array $abiArray): array
    {
        foreach ($abiArray as $item) {
            if (!is_array($item)) {
                continue;
            }
            if (isset($item['type']) && 'constructor' === $item['type']) {
                return $item;
            }
        }

        return [];
    }

    /**
     * 提取事件定义
     *
     * @param array<string, mixed> $abiArray
     * @return array<string, array<string, mixed>>
     */
    private function extractEvents(array $abiArray): array
    {
        $events = [];

        foreach ($abiArray as $item) {
            if (!is_array($item)) {
                continue;
            }
            if (isset($item['type'], $item['name']) && 'event' === $item['type']) {
                $events[$item['name']] = $item;
            }
        }

        return $events;
    }
}
