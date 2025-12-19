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

namespace Tourze\Web3PHP\Contracts;

use Tourze\Web3PHP\Formatters\IntegerFormatter;

/**
 * 数组编码器
 *
 * 负责处理 Solidity 数组类型的编码,包括动态数组和静态数组
 */
class ArrayEncoder
{
    /**
     * 使用偏移量编码
     *
     * @param string       $type
     * @param SolidityType $solidityType
     * @param mixed        $encoded
     * @param int          $offset
     *
     * @return string
     */
    public function encodeWithOffset(string $type, SolidityType $solidityType, mixed $encoded, int $offset): string
    {
        if ($solidityType->isDynamicArray($type)) {
            return $this->encodeDynamicArray($type, $solidityType, $encoded, $offset);
        }

        if ($solidityType->isStaticArray($type)) {
            return $this->encodeStaticArray($type, $solidityType, $encoded, $offset);
        }

        return $encoded;
    }

    /**
     * 编码动态数组
     *
     * @param string       $type
     * @param SolidityType $solidityType
     * @param mixed        $encoded
     * @param int          $offset
     *
     * @return string
     */
    private function encodeDynamicArray(string $type, SolidityType $solidityType, mixed $encoded, int $offset): string
    {
        $result = $this->encodeArrayWithPointers($type, $solidityType, $encoded, $offset, true);

        return mb_substr($result, 64);
    }

    /**
     * 编码静态数组
     *
     * @param string       $type
     * @param SolidityType $solidityType
     * @param mixed        $encoded
     * @param int          $offset
     *
     * @return string
     */
    private function encodeStaticArray(string $type, SolidityType $solidityType, mixed $encoded, int $offset): string
    {
        return $this->encodeArrayWithPointers($type, $solidityType, $encoded, $offset, false);
    }

    /**
     * 编码数组带指针
     *
     * @param string       $type
     * @param SolidityType $solidityType
     * @param mixed        $encoded
     * @param int          $offset
     * @param bool         $isDynamic
     *
     * @return string
     */
    private function encodeArrayWithPointers(string $type, SolidityType $solidityType, mixed $encoded, int $offset, bool $isDynamic): string
    {
        $nestedName = $solidityType->nestedName($type);
        $result = $isDynamic ? $encoded[0] : '';

        if ($solidityType->isDynamicArray($nestedName)) {
            $result .= $this->buildArrayPointersForType($encoded, $solidityType, $type, $offset, $isDynamic);
        }

        $result .= $this->encodeArrayElements($encoded, $nestedName, $solidityType, $offset, $result);

        return $result;
    }

    /**
     * 为类型构建数组指针
     *
     * @param mixed        $encoded
     * @param SolidityType $solidityType
     * @param string       $type
     * @param int          $offset
     * @param bool         $isDynamic
     *
     * @return string
     */
    private function buildArrayPointersForType(mixed $encoded, SolidityType $solidityType, string $type, int $offset, bool $isDynamic): string
    {
        return $isDynamic
            ? $this->buildDynamicArrayPointers($encoded, $solidityType, $type, $offset)
            : $this->buildStaticArrayPointers($encoded, $solidityType, $type, $offset);
    }

    /**
     * 构建动态数组指针
     *
     * @param mixed        $encoded
     * @param SolidityType $solidityType
     * @param string       $type
     * @param int          $offset
     *
     * @return string
     */
    private function buildDynamicArrayPointers(mixed $encoded, SolidityType $solidityType, string $type, int $offset): string
    {
        return $this->buildPointers($encoded, $solidityType, $type, $offset, 2);
    }

    /**
     * 构建静态数组指针
     *
     * @param mixed        $encoded
     * @param SolidityType $solidityType
     * @param string       $type
     * @param int          $offset
     *
     * @return string
     */
    private function buildStaticArrayPointers(mixed $encoded, SolidityType $solidityType, string $type, int $offset): string
    {
        return $this->buildPointers($encoded, $solidityType, $type, $offset, 0);
    }

    /**
     * 构建指针字符串
     *
     * @param mixed        $encoded
     * @param SolidityType $solidityType
     * @param string       $type
     * @param int          $offset
     * @param int          $initialPrevLength
     *
     * @return string
     */
    private function buildPointers(mixed $encoded, SolidityType $solidityType, string $type, int $offset, int $initialPrevLength): string
    {
        $result = '';
        $previousLength = $initialPrevLength;
        $staticPartLength = $solidityType->staticPartLength($type);
        $isDynamic = ($initialPrevLength > 0);

        foreach ($encoded as $i => $item) {
            if ($i > 0) {
                $previousLength += $this->getPrevValueLength($encoded[$i - 1], $isDynamic);
            }
            $result .= IntegerFormatter::format($offset + $i * $staticPartLength + $previousLength * 32);
        }

        return $result;
    }

    /**
     * 获取前一个值的长度
     *
     * @param mixed $prevValue
     * @param bool  $isDynamic
     *
     * @return int
     */
    private function getPrevValueLength(mixed $prevValue, bool $isDynamic): int
    {
        if ($isDynamic) {
            return (int) abs($prevValue[0]);
        }

        return is_array($prevValue) ? (int) abs($prevValue[0]) : (int) abs($prevValue);
    }

    /**
     * 编码数组元素
     *
     * @param mixed        $encoded
     * @param string       $nestedName
     * @param SolidityType $solidityType
     * @param int          $offset
     * @param string       $result
     *
     * @return string
     */
    private function encodeArrayElements(mixed $encoded, string $nestedName, SolidityType $solidityType, int $offset, string $result): string
    {
        $elementResult = '';
        $elementsCount = count($encoded);

        for ($i = 0; $i < $elementsCount; ++$i) {
            $additionalOffset = (int) floor(mb_strlen($result . $elementResult) / 2);
            $elementResult .= $this->encodeWithOffset($nestedName, $solidityType, $encoded[$i], $offset + $additionalOffset);
        }

        return $elementResult;
    }
}
