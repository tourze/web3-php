<?php

declare(strict_types=1);

/**
 * 此文件是 web3.php 包的一部分。
 *
 * (c) Kuan-Cheng,Lai <alk03073135@gmail.com>
 *
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */

namespace Tourze\Web3PHP;

use Tourze\Web3PHP\Exception\InvalidArgumentException;

/**
 * JSON 工具类
 * @phpstan-type JsonMethodInput object|array<string, mixed>
 */
class JsonUtils
{
    /**
     * JSON方法转字符串
     */
    public static function jsonMethodToString(mixed $json): string
    {
        $array = self::normalizeJsonInput($json);

        if (isset($array['name']) && str_contains($array['name'], '(')) {
            return $array['name'];
        }

        $inputs = $array['inputs'] ?? [];
        $types = [];

        foreach ($inputs as $param) {
            $type = self::extractTypeFromParam($param);
            if (null !== $type) {
                $types[] = $type;
            }
        }

        return $array['name'] . '(' . implode(',', $types) . ')';
    }

    /**
     * 标准化JSON输入
     * @return array<string, mixed>
     */
    private static function normalizeJsonInput(mixed $json): array
    {
        if ($json instanceof \stdClass) {
            return (array) $json;
        }

        if (!is_array($json)) {
            throw new InvalidArgumentException('jsonMethodToString json must be array or stdClass.');
        }

        return $json;
    }

    /**
     * 从参数中提取类型
     * @param object|array<string, mixed> $param
     */
    private static function extractTypeFromParam(object|array $param): ?string
    {
        if (is_object($param) && property_exists($param, 'type')) {
            return $param->type;
        }

        if (is_array($param) && isset($param['type'])) {
            return $param['type'];
        }

        return null;
    }

    /**
     * JSON转数组
     * @return mixed
     */
    public static function jsonToArray(mixed $json): mixed
    {
        if ($json instanceof \stdClass) {
            $json = (array) $json;
        }

        if (is_array($json)) {
            $result = [];
            foreach ($json as $key => $value) {
                $result[$key] = self::jsonToArray($value);
            }

            return $result;
        }

        return $json;
    }
}
