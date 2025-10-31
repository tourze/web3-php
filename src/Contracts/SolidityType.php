<?php

/**
 * 此文件是 web3.php 包的一部分。
 *
 * (c) Kuan-Cheng,Lai <alk03073135@gmail.com>
 *
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */

namespace Tourze\Web3PHP\Contracts;

use Tourze\Web3PHP\Exception\InvalidArgumentException;
use Tourze\Web3PHP\Formatters\IntegerFormatter;
use Tourze\Web3PHP\Utils;

abstract class SolidityType
{
    /**
     * 构造函数
     * @param mixed $name
     */
    // public function  __construct() {}

    /**
     * 获取属性
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        $method = 'get' . ucfirst($name);

        if (method_exists($this, $method)) {
            /** @var callable $callable */
            $callable = [$this, $method];

            return call_user_func_array($callable, []);
        }

        return false;
    }

    /**
     * 设置属性
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value): void
    {
        $method = 'set' . ucfirst($name);

        if (method_exists($this, $method)) {
            /** @var callable $callable */
            $callable = [$this, $method];
            call_user_func_array($callable, [$value]);

            return;
        }
    }

    /**
     * callStatic
     *
     * @param string $name
     */
    // public static function __callStatic($name, $arguments) {}

    /**
     * nestedTypes
     *
     * @param string $name
     * @return array<int, string>|false
     */
    public function nestedTypes($name)
    {
        if (!is_string($name)) {
            throw new InvalidArgumentException('nestedTypes name must string.');
        }
        $matches = [];

        if (preg_match_all('/(\[[0-9]*\])/', $name, $matches, PREG_PATTERN_ORDER) >= 1) {
            return $matches[0];
        }

        return false;
    }

    /**
     * nestedName
     *
     * @param string $name
     *
     * @return string
     */
    public function nestedName($name)
    {
        if (!is_string($name)) {
            throw new InvalidArgumentException('nestedName name must string.');
        }
        $nestedTypes = $this->nestedTypes($name);

        if (false === $nestedTypes) {
            return $name;
        }

        return mb_substr($name, 0, mb_strlen($name) - mb_strlen($nestedTypes[count($nestedTypes) - 1]));
    }

    /**
     * isDynamicArray
     *
     * @param string $name
     *
     * @return bool
     */
    public function isDynamicArray($name)
    {
        $nestedTypes = $this->nestedTypes($name);

        if (false === $nestedTypes) {
            return false;
        }

        // A type is dynamic if it contains any dynamic array dimension
        foreach ($nestedTypes as $type) {
            if ('[]' === $type) {
                return true;
            }
        }

        return false;
    }

    /**
     * isStaticArray
     *
     * @param string $name
     *
     * @return bool
     */
    public function isStaticArray($name)
    {
        $nestedTypes = $this->nestedTypes($name);

        return false !== $nestedTypes && 1 === preg_match('/[0-9]{1,}/', $nestedTypes[count($nestedTypes) - 1]);
    }

    /**
     * staticArrayLength
     *
     * @param string $name
     *
     * @return int
     */
    public function staticArrayLength($name)
    {
        $nestedTypes = $this->nestedTypes($name);

        if (false === $nestedTypes) {
            return 1;
        }
        $match = [];

        if (1 === preg_match('/[0-9]{1,}/', $nestedTypes[count($nestedTypes) - 1], $match)) {
            return (int) $match[0];
        }

        return 1;
    }

    /**
     * staticPartLength
     *
     * @param string $name
     *
     * @return int
     */
    public function staticPartLength($name)
    {
        $nestedTypes = $this->nestedTypes($name);

        if (false === $nestedTypes) {
            $nestedTypes = ['[1]'];
        }
        $count = 32;

        foreach ($nestedTypes as $type) {
            // Extract number from brackets like [10], [5], etc.
            if (1 === preg_match('/\[(\d+)\]/', $type, $matches)) {
                $num = (int) $matches[1];
            } else {
                // 动态数组或无效格式
                $num = 1;
            }
            $count *= $num;
        }

        return $count;
    }

    /**
     * isDynamicType
     *
     * @return bool
     */
    public function isDynamicType()
    {
        return false;
    }

    /**
     * 输入格式化
     *
     * @param mixed $value
     * @param string $name
     * @return string
     */
    public function inputFormat($value, $name): string
    {
        return (string) $value;
    }

    /**
     * 输出格式化
     *
     * @param mixed $value
     * @param string $name
     * @return string
     */
    public function outputFormat($value, $name): string
    {
        return (string) $value;
    }

    /**
     * 编码处理
     *
     * @param mixed $value
     * @param string $name
     *
     * @return string|array<int, mixed>
     */
    public function encode($value, $name)
    {
        if ($this->isDynamicArray($name)) {
            return $this->encodeDynamicArray($value, $name);
        }

        if ($this->isStaticArray($name)) {
            return $this->encodeStaticArray($value, $name);
        }

        return $this->inputFormat($value, $name);
    }

    /**
     * 编码动态数组
     * @param mixed $value
     * @return array<int, mixed>
     */
    private function encodeDynamicArray($value, string $name): array
    {
        $result = [IntegerFormatter::format(count($value))];
        $nestedName = $this->nestedName($name);

        foreach ($value as $val) {
            $result = $this->addEncodedValue($result, $this->encode($val, $nestedName));
        }

        return $result;
    }

    /**
     * 编码静态数组
     * @param mixed $value
     * @return array<int, mixed>
     */
    private function encodeStaticArray($value, string $name): array
    {
        /** @var array<int, mixed> $result */
        $result = [];
        $nestedName = $this->nestedName($name);

        foreach ($value as $val) {
            $result = $this->addEncodedValue($result, $this->encode($val, $nestedName));
        }

        return $result;
    }

    /**
     * 添加编码值
     * @param array<mixed> $result
     * @param mixed $encoded
     * @return array<mixed>
     */
    private function addEncodedValue(array $result, $encoded): array
    {
        if (is_array($encoded)) {
            return array_merge($result, $encoded);
        }
        $result[] = $encoded;

        return $result;
    }

    /**
     * 解码处理
     *
     * @param string $value
     * @param int|string $offset
     * @param string $name
     *
     * @return mixed
     */
    public function decode($value, $offset, $name)
    {
        $offset = (int) $offset;

        if ($this->isDynamicArray($name)) {
            return $this->decodeDynamicArray($value, $offset, $name);
        }

        if ($this->isStaticArray($name)) {
            return $this->decodeStaticArray($value, $offset, $name);
        }

        if ($this->isDynamicType()) {
            return $this->decodeDynamicType($value, $offset, $name);
        }

        return $this->decodeStaticType($value, $offset, $name);
    }

    /**
     * 解码动态数组
     * @return array<int, mixed>
     */
    private function decodeDynamicArray(string $value, int $offset, string $name): array
    {
        $arrayOffset = $this->extractOffset($value, $offset);
        $length = $this->extractLength($value, $arrayOffset);

        return $this->decodeArrayElements($value, $arrayOffset + 32, $length, $this->nestedName($name));
    }

    /**
     * 解码静态数组
     * @return array<int, mixed>
     */
    private function decodeStaticArray(string $value, int $offset, string $name): array
    {
        $length = $this->staticArrayLength($name);

        return $this->decodeArrayElements($value, $offset, $length, $this->nestedName($name));
    }

    /**
     * 解码数组元素
     * @return array<int, mixed>
     */
    private function decodeArrayElements(string $value, int $arrayStart, int $length, string $nestedName): array
    {
        $elementLength = $this->calculateRoundedLength($this->staticPartLength($nestedName));
        $result = [];

        for ($i = 0; $i < $length * $elementLength; $i += $elementLength) {
            $result[] = $this->decode($value, $arrayStart + $i, $nestedName);
        }

        return $result;
    }

    /**
     * 解码动态类型
     */
    private function decodeDynamicType(string $value, int $offset, string $name): string
    {
        $dynamicOffset = $this->extractOffset($value, $offset);
        $length = $this->extractLength($value, $dynamicOffset);
        $roundedLength = $this->calculateRoundedLength($length);

        $param = mb_substr($value, $dynamicOffset * 2, (1 + $roundedLength) * 64);

        return $this->outputFormat($param, $name);
    }

    /**
     * 解码静态类型
     */
    private function decodeStaticType(string $value, int $offset, string $name): string
    {
        $length = $this->staticPartLength($name);
        $param = mb_substr($value, $offset * 2, $length * 2);

        return $this->outputFormat($param, $name);
    }

    /**
     * 提取偏移量
     */
    private function extractOffset(string $value, int $offset): int
    {
        $offsetBn = Utils::toBn('0x' . mb_substr($value, $offset * 2, 64));
        if (is_array($offsetBn)) {
            throw new InvalidArgumentException('Unexpected array result from toBn');
        }

        return (int) $offsetBn->toString();
    }

    /**
     * 提取长度
     */
    private function extractLength(string $value, int $offset): int
    {
        $lengthBn = Utils::toBn('0x' . mb_substr($value, $offset * 2, 64));
        if (is_array($lengthBn)) {
            throw new InvalidArgumentException('Unexpected array result from toBn');
        }

        return (int) $lengthBn->toString();
    }

    /**
     * 计算圆整长度
     */
    private function calculateRoundedLength(int $length): int
    {
        return (int) floor(($length + 31) / 32);
    }
}
