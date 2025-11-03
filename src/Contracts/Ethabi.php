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

class Ethabi
{
    /**
     * 类型集合
     *
     * @var array<string, mixed>
     */
    protected $types = [];

    /**
     * 构造函数
     *
     * @param array<string, mixed> $types
     */
    public function __construct($types = [])
    {
        if (!is_array($types)) {
            $types = [];
        }
        $this->types = $types;
    }

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
     * @param array<int, mixed>  $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        throw new InvalidArgumentException('Method not found: ' . $name);
    }

    /**
     * encodeFunctionSignature
     *
     * @param string|\stdClass|array<string, mixed> $functionName
     *
     * @return string
     */
    public function encodeFunctionSignature($functionName)
    {
        if (!is_string($functionName)) {
            $functionName = Utils::jsonMethodToString($functionName);
        }

        $hash = Utils::sha3($functionName);
        if (null === $hash) {
            throw new InvalidArgumentException('Unable to generate function signature hash.');
        }

        return mb_substr($hash, 0, 10);
    }

    /**
     * encodeEventSignature
     * TODO: Fix same event name with different params
     *
     * @param string|\stdClass|array<string, mixed> $functionName
     *
     * @return string
     */
    public function encodeEventSignature($functionName)
    {
        if (!is_string($functionName)) {
            $functionName = Utils::jsonMethodToString($functionName);
        }

        $hash = Utils::sha3($functionName);
        if (null === $hash) {
            throw new InvalidArgumentException('Unable to generate event signature hash.');
        }

        return $hash;
    }

    /**
     * encodeParameter
     *
     * @param string $type
     * @param mixed $param
     *
     * @return string
     */
    public function encodeParameter($type, $param)
    {
        if (!is_string($type)) {
            throw new InvalidArgumentException('The type to encodeParameter must be string.');
        }

        return $this->encodeParameters([$type], [$param]);
    }

    /**
     * encodeParameters
     *
     * @param \stdClass|array<string, mixed>|array<int, string> $types
     * @param array<int, mixed>           $params
     *
     * @return string
     */
    public function encodeParameters($types, $params)
    {
        $encodingContext = $this->prepareEncodingContext($types, $params);

        return '0x' . $this->encodeMultiWithOffset(
            $encodingContext['types'],
            $encodingContext['solidityTypes'],
            $encodingContext['encodes'],
            $encodingContext['dynamicOffset']
        );
    }

    /**
     * 准备编码上下文
     *
     * @param mixed $types
     * @param array<int, mixed> $params
     * @return array{types: array<int, string>, solidityTypes: array<int, SolidityType>, encodes: array<int, mixed>, dynamicOffset: int}
     */
    private function prepareEncodingContext($types, $params): array
    {
        $typeArray = $this->normalizeTypes($types);
        $this->validateParameters($typeArray, $params);

        $solidityTypes = $this->getSolidityTypes($typeArray);
        $encodes = $this->encodeAllParameters($solidityTypes, $params, $typeArray);
        $dynamicOffset = $this->calculateDynamicOffset($solidityTypes, $typeArray);

        return [
            'types' => $typeArray,
            'solidityTypes' => $solidityTypes,
            'encodes' => $encodes,
            'dynamicOffset' => $dynamicOffset,
        ];
    }

    /**
     * 标准化类型数组
     *
     * @param mixed $types
     * @return array<int, string>
     */
    private function normalizeTypes($types): array
    {
        if ($types instanceof \stdClass && isset($types->inputs)) {
            $types = Utils::jsonToArray($types);
        }

        if (is_array($types) && isset($types['inputs'])) {
            return $this->extractTypesFromInputs($types['inputs']);
        }

        return array_values($types);
    }

    /**
     * 从输入中提取类型
     *
     * @param array<mixed> $inputs
     * @return array<string>
     */
    private function extractTypesFromInputs($inputs): array
    {
        $types = [];
        foreach ($inputs as $input) {
            if (isset($input['type'])) {
                $types[] = $input['type'];
            }
        }

        return $types;
    }

    /**
     * 验证参数
     *
     * @param array<int, string> $types
     * @param array<int, mixed> $params
     */
    private function validateParameters($types, $params): void
    {
        if (!is_array($types) || !is_array($params)) {
            throw new InvalidArgumentException('Types and params must be arrays.');
        }

        if (count($types) !== count($params)) {
            throw new InvalidArgumentException('encodeParameters number of types must equal to number of params.');
        }
    }

    /**
     * 编码所有参数
     *
     * @param array<int, SolidityType> $solidityTypes
     * @param array<int, mixed> $params
     * @param array<int, string> $typeArray
     * @return array<int, array<int, mixed>|string>
     */
    private function encodeAllParameters($solidityTypes, $params, $typeArray): array
    {
        $encodes = [];
        foreach ($solidityTypes as $key => $type) {
            $encodes[$key] = $type->encode($params[$key], $typeArray[$key]);
        }

        return $encodes;
    }

    /**
     * 计算动态偏移量
     *
     * @param array<int, SolidityType> $solidityTypes
     * @param array<int, string> $typeArray
     * @return int
     */
    private function calculateDynamicOffset($solidityTypes, $typeArray): int
    {
        $dynamicOffset = 0;
        foreach ($solidityTypes as $key => $type) {
            if ($type->isDynamicType() || $type->isDynamicArray($typeArray[$key])) {
                $dynamicOffset += 32;
            } else {
                $staticLength = $type->staticPartLength($typeArray[$key]);
                $dynamicOffset += floor(($staticLength + 31) / 32) * 32;
            }
        }

        return (int) $dynamicOffset;
    }

    /**
     * decodeParameter
     *
     * @param string $type
     * @param mixed $param
     *
     * @return string
     */
    public function decodeParameter($type, $param)
    {
        if (!is_string($type)) {
            throw new InvalidArgumentException('The type to decodeParameter must be string.');
        }

        return $this->decodeParameters([$type], $param)[0];
    }

    /**
     * decodeParameters
     *
     * @param mixed $types
     * @param string $param
     *
     * @return array<int|string, mixed>
     */
    public function decodeParameters($types, $param)
    {
        if (!is_string($param)) {
            throw new InvalidArgumentException('The type or param to decodeParameters must be string.');
        }

        $decodingInfo = $this->prepareDecodingInfo($types);
        $solidityTypes = $this->getSolidityTypes($decodingInfo['types']);
        $offsets = $this->calculateOffsets($solidityTypes, $decodingInfo['types']);

        return $this->decodeAllParameters(
            $solidityTypes,
            $decodingInfo['types'],
            $offsets,
            Utils::stripZero($param),
            $decodingInfo['outputs'] ?? null
        );
    }

    /**
     * 准备解码信息
     *
     * @param mixed $types
     * @return array{types: array<string>, outputs: array<mixed>|null}
     */
    private function prepareDecodingInfo($types): array
    {
        if ($types instanceof \stdClass && isset($types->outputs)) {
            $types = Utils::jsonToArray($types);
        }

        if (is_array($types) && isset($types['outputs'])) {
            return [
                'types' => $this->extractTypesFromOutputs($types['outputs']),
                'outputs' => $types['outputs'],
            ];
        }

        return [
            'types' => $types,
            'outputs' => null,
        ];
    }

    /**
     * 从输出中提取类型
     *
     * @param array<mixed> $outputs
     * @return array<string>
     */
    private function extractTypesFromOutputs($outputs): array
    {
        $types = [];
        foreach ($outputs as $output) {
            if (isset($output['type'])) {
                $types[] = $output['type'];
            }
        }

        return $types;
    }

    /**
     * 计算偏移量
     *
     * @param array<int, SolidityType> $solidityTypes
     * @param array<string> $types
     * @return array<int>
     */
    private function calculateOffsets($solidityTypes, $types): array
    {
        $typesLength = count($types);
        $offsets = array_fill(0, $typesLength, 0);

        // 计算累积偏移
        for ($i = 0; $i < $typesLength; ++$i) {
            $offsets[$i] = $solidityTypes[$i]->staticPartLength($types[$i]);
        }
        for ($i = 1; $i < $typesLength; ++$i) {
            $offsets[$i] += $offsets[$i - 1];
        }

        // 调整为相对偏移
        for ($i = 0; $i < $typesLength; ++$i) {
            $offsets[$i] -= $solidityTypes[$i]->staticPartLength($types[$i]);
        }

        return $offsets;
    }

    /**
     * 解码所有参数
     *
     * @param array<int, SolidityType> $solidityTypes
     * @param array<string> $types
     * @param array<int> $offsets
     * @param string $param
     * @param array<mixed>|null $outputs
     * @return array<int|string, mixed>
     */
    private function decodeAllParameters($solidityTypes, $types, $offsets, $param, $outputs): array
    {
        $param = mb_strtolower($param);
        $result = [];

        foreach ($solidityTypes as $i => $solidityType) {
            $decodedValue = $solidityType->decode($param, $offsets[$i], $types[$i]);
            $key = $this->getResultKey($i, $outputs);
            $result[$key] = $decodedValue;
        }

        return $result;
    }

    /**
     * 获取结果键名
     *
     * @param int $index
     * @param array<mixed>|null $outputs
     * @return int|string
     */
    private function getResultKey($index, $outputs)
    {
        if (null !== $outputs
            && isset($outputs[$index]['name'])
            && '' !== $outputs[$index]['name']) {
            return $outputs[$index]['name'];
        }

        return $index;
    }

    /**
     * 获取Solidity类型
     *
     * @param array<int, string> $types
     *
     * @return array<int, SolidityType>
     */
    protected function getSolidityTypes($types)
    {
        if (!is_array($types)) {
            throw new InvalidArgumentException('Types must be array');
        }

        $solidityTypes = [];
        foreach ($types as $key => $type) {
            $solidityTypes[$key] = $this->createSolidityType($type);
        }

        return $solidityTypes;
    }

    /**
     * 创建Solidity类型实例
     *
     * @param string $type
     * @return SolidityType
     */
    private function createSolidityType($type)
    {
        $baseType = $this->extractBaseType($type);

        if (!isset($this->types[$baseType])) {
            throw new InvalidArgumentException('Unsupport solidity parameter type: ' . $type);
        }

        $typeInstance = $this->instantiateType($this->types[$baseType]);

        // 如果是IType实例但不匹配当前类型，尝试处理特殊类型
        if ($typeInstance instanceof Types\IType && !$typeInstance->isType($type)) {
            return $this->handleSpecialType($baseType, $type);
        }

        return $typeInstance;
    }

    /**
     * 提取基础类型
     *
     * @param string $type
     * @return string
     */
    private function extractBaseType($type): string
    {
        $match = [];
        if (1 === preg_match('/^([a-zA-Z]+)/', $type, $match)) {
            return $match[0];
        }

        throw new InvalidArgumentException('Invalid type format: ' . $type);
    }

    /**
     * 实例化类型
     *
     * @param mixed $typeDefinition
     * @return SolidityType
     */
    private function instantiateType($typeDefinition)
    {
        return is_string($typeDefinition) ? new $typeDefinition() : $typeDefinition;
    }

    /**
     * 处理特殊类型
     *
     * @param string $baseType
     * @param string $fullType
     * @return SolidityType
     */
    private function handleSpecialType($baseType, $fullType)
    {
        if ('bytes' === $baseType) {
            return $this->instantiateType($this->types['dynamicBytes']);
        }

        throw new InvalidArgumentException('Unsupport solidity parameter type: ' . $fullType);
    }

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
    protected function encodeWithOffset($type, $solidityType, $encoded, int $offset)
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
     * @param mixed $type
     * @param mixed $solidityType
     * @param mixed $encoded
     */
    private function encodeDynamicArray($type, $solidityType, $encoded, int $offset): string
    {
        $result = $this->encodeArrayWithPointers($type, $solidityType, $encoded, $offset, true);

        return mb_substr($result, 64);
    }

    /**
     * 编码静态数组
     * @param mixed $type
     * @param mixed $solidityType
     * @param mixed $encoded
     */
    private function encodeStaticArray($type, $solidityType, $encoded, int $offset): string
    {
        return $this->encodeArrayWithPointers($type, $solidityType, $encoded, $offset, false);
    }

    /**
     * 编码数组带指针
     * @param mixed $type
     * @param mixed $solidityType
     * @param mixed $encoded
     */
    private function encodeArrayWithPointers($type, $solidityType, $encoded, int $offset, bool $isDynamic): string
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
     * @param mixed $encoded
     * @param mixed $solidityType
     * @param mixed $type
     */
    private function buildArrayPointersForType($encoded, $solidityType, $type, int $offset, bool $isDynamic): string
    {
        return $isDynamic
            ? $this->buildDynamicArrayPointers($encoded, $solidityType, $type, $offset)
            : $this->buildStaticArrayPointers($encoded, $solidityType, $type, $offset);
    }

    /**
     * 构建动态数组指针
     */
    private function buildDynamicArrayPointers(mixed $encoded, SolidityType $solidityType, string $type, int $offset): string
    {
        return $this->buildPointers($encoded, $solidityType, $type, $offset, 2);
    }

    /**
     * 构建静态数组指针
     */
    private function buildStaticArrayPointers(mixed $encoded, SolidityType $solidityType, string $type, int $offset): string
    {
        return $this->buildPointers($encoded, $solidityType, $type, $offset, 0);
    }

    /**
     * 构建指针字符串
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
     * @param mixed $encoded
     * @param mixed $nestedName
     * @param mixed $solidityType
     */
    private function encodeArrayElements($encoded, $nestedName, $solidityType, int $offset, string $result): string
    {
        $elementResult = '';
        $elementsCount = count($encoded);

        for ($i = 0; $i < $elementsCount; ++$i) {
            $additionalOffset = (int) floor(mb_strlen($result . $elementResult) / 2);
            $elementResult .= $this->encodeWithOffset($nestedName, $solidityType, $encoded[$i], $offset + $additionalOffset);
        }

        return $elementResult;
    }

    /**
     * 多重偏移量编码
     *
     * @param array<int, string> $types
     * @param array<int, SolidityType> $solidityTypes
     * @param array<int, array<int, mixed>|string> $encodes
     * @param int   $dynamicOffset
     *
     * @return string
     */
    protected function encodeMultiWithOffset($types, $solidityTypes, $encodes, $dynamicOffset)
    {
        $staticPart = '';
        $dynamicParts = [];
        $currentOffset = $dynamicOffset;

        foreach ($solidityTypes as $key => $type) {
            if ($type->isDynamicType() || $type->isDynamicArray($types[$key])) {
                $staticPart .= IntegerFormatter::format($currentOffset);
                $encodedElement = $this->encodeWithOffset($types[$key], $type, $encodes[$key], $currentOffset);
                $dynamicParts[] = $encodedElement;
                $currentOffset += (int) floor(mb_strlen($encodedElement) / 2);
            } else {
                $staticPart .= $this->encodeWithOffset($types[$key], $type, $encodes[$key], $currentOffset);
            }
        }

        return $staticPart . implode('', $dynamicParts);
    }
}
