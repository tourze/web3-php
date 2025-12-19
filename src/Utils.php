<?php

/**
 * 此文件是 web3.php 包的一部分。
 *
 * (c) Kuan-Cheng,Lai <alk03073135@gmail.com>
 *
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */

namespace Tourze\Web3PHP;

use kornrunner\Keccak;
use phpseclib3\Math\BigInteger as BigNumber;
use Tourze\Web3PHP\Exception\InvalidArgumentException;

/**
 * 工具类，提供十六进制转换、地址验证、Wei转换等功能
 *
 * @phpstan-type FractionalNumber array{0: BigNumber, 1: BigNumber, 2: int, 3: BigNumber|false}
 * @phpstan-type JsonMethodInput object|array<string, mixed>
 * @phpstan-ignore complexity.classLike
 */
class Utils
{
    /**
     * SHA3 零值哈希
     */
    public const SHA3_NULL_HASH = 'c5d2460186f7233c927e7db2dcc703c0e500b653ca82273b7bfad8045d85a470';

    /**
     * 单位常量 - 来自 ethjs-unit
     * @var array<string, string>
     */
    public const UNITS = [
        'noether' => '0',
        'wei' => '1',
        'kwei' => '1000',
        'Kwei' => '1000',
        'babbage' => '1000',
        'femtoether' => '1000',
        'mwei' => '1000000',
        'Mwei' => '1000000',
        'lovelace' => '1000000',
        'picoether' => '1000000',
        'gwei' => '1000000000',
        'Gwei' => '1000000000',
        'shannon' => '1000000000',
        'nanoether' => '1000000000',
        'nano' => '1000000000',
        'szabo' => '1000000000000',
        'microether' => '1000000000000',
        'micro' => '1000000000000',
        'finney' => '1000000000000000',
        'milliether' => '1000000000000000',
        'milli' => '1000000000000000',
        'ether' => '1000000000000000000',
        'kether' => '1000000000000000000000',
        'grand' => '1000000000000000000000',
        'mether' => '1000000000000000000000000',
        'gether' => '1000000000000000000000000000',
        'tether' => '1000000000000000000000000000000',
    ];

    /**
     * 十六进制编码
     * @param mixed $value
     */
    public static function toHex(mixed $value, bool $isPrefix = false): string
    {
        self::validateHexInput($value);
        $hex = self::convertToHex($value);

        return $isPrefix ? '0x' . $hex : $hex;
    }

    /**
     * 验证十六进制输入
     */
    private static function validateHexInput(mixed $value): void
    {
        if (!is_string($value) && !is_int($value) && !($value instanceof BigNumber)) {
            throw new InvalidArgumentException('The value to toHex function is not support.');
        }
    }

    /**
     * 转换为十六进制
     */
    private static function convertToHex(mixed $value): string
    {
        if (is_numeric($value)) {
            return self::convertNumericToHex($value);
        }

        if (is_string($value)) {
            return self::convertStringToHex($value);
        }

        if ($value instanceof BigNumber) {
            return self::convertBigNumberToHex($value);
        }

        throw new InvalidArgumentException('Unsupported value type for hex conversion');
    }

    /**
     * 转换数值为十六进制
     */
    private static function convertNumericToHex(mixed $value): string
    {
        $bn = self::toBn($value);
        if (is_array($bn)) {
            throw new InvalidArgumentException('Numeric values cannot be fractional for hex conversion');
        }

        $hex = $bn->toHex(true);

        return self::normalizeHex($hex);
    }

    /**
     * 转换字符串为十六进制
     */
    private static function convertStringToHex(string $value): string
    {
        $cleanValue = self::stripZero($value);
        $packed = unpack('H*', $cleanValue);

        return is_array($packed) ? implode('', $packed) : '';
    }

    /**
     * 转换BigNumber为十六进制
     */
    private static function convertBigNumberToHex(BigNumber $value): string
    {
        $hex = $value->toHex(true);

        return self::normalizeHex($hex);
    }

    /**
     * 规范化十六进制字符串
     */
    private static function normalizeHex(string $hex): string
    {
        if ('' === $hex || '0' === $hex) {
            return '0';
        }

        $result = preg_replace('/^0+(?!$)/', '', $hex);

        return $result ?? $hex;
    }

    /**
     * 十六进制转二进制
     */
    public static function hexToBin(mixed $value): string
    {
        if (!is_string($value)) {
            throw new InvalidArgumentException('The value to hexToBin function must be string.');
        }

        if (self::isZeroPrefixed($value)) {
            $value = substr($value, 2);
        }

        return pack('H*', $value);
    }

    /**
     * 检查是否以零前缀开头
     */
    public static function isZeroPrefixed(mixed $value): bool
    {
        if (!is_string($value)) {
            throw new InvalidArgumentException('The value to isZeroPrefixed function must be string.');
        }

        return str_starts_with($value, '0x') || str_starts_with($value, '0X');
    }

    /**
     * 移除0x前缀
     */
    public static function stripZero(string $value): string
    {
        if (!self::isZeroPrefixed($value)) {
            return $value;
        }

        return substr($value, 2);
    }

    /**
     * 检查是否为负数
     */
    public static function isNegative(mixed $value): bool
    {
        if (!is_string($value)) {
            throw new InvalidArgumentException('The value to isNegative function must be string.');
        }

        return str_starts_with($value, '-');
    }

    /**
     * 验证以太坊地址
     */
    public static function isAddress(mixed $value): bool
    {
        if (!is_string($value)) {
            throw new InvalidArgumentException('The value to isAddress function must be string.');
        }

        return self::isValidAddressFormat($value)
               && (self::isSimpleCase($value) || self::isAddressChecksum($value));
    }

    /**
     * 检查地址格式是否有效
     */
    private static function isValidAddressFormat(string $value): bool
    {
        return 1 === preg_match('/^(0x|0X)?[a-f0-9A-F]{40}$/', $value);
    }

    /**
     * 检查是否为简单情况（全大写或全小写）
     */
    private static function isSimpleCase(string $value): bool
    {
        return 1 === preg_match('/^(0x|0X)?[a-f0-9]{40}$/', $value)
               || 1 === preg_match('/^(0x|0X)?[A-F0-9]{40}$/', $value);
    }

    /**
     * 检查地址校验和
     */
    public static function isAddressChecksum(mixed $value): bool
    {
        if (!is_string($value)) {
            throw new InvalidArgumentException('The value to isAddressChecksum function must be string.');
        }

        $checksumData = self::prepareChecksumData($value);
        if (null === $checksumData) {
            return false;
        }

        return self::validateAllChecksumChars($checksumData['hash'], $checksumData['value']);
    }

    /**
     * 准备校验和数据
     * @return array{hash: string, value: string}|null
     */
    private static function prepareChecksumData(string $value): ?array
    {
        $cleanValue = self::stripZero($value);
        $hashResult = self::sha3(mb_strtolower($cleanValue));

        if (null === $hashResult) {
            return null;
        }

        return [
            'hash' => self::stripZero($hashResult),
            'value' => $cleanValue,
        ];
    }

    /**
     * 验证所有校验和字符
     */
    private static function validateAllChecksumChars(string $hash, string $value): bool
    {
        for ($i = 0; $i < 40; ++$i) {
            $hashValue = intval($hash[$i], 16);

            if ($hashValue > 7) {
                if (mb_strtoupper($value[$i]) !== $value[$i]) {
                    return false;
                }
            } elseif (mb_strtolower($value[$i]) !== $value[$i]) {
                return false;
            }
        }

        return true;
    }

    /**
     * 转换为校验和地址
     */
    public static function toChecksumAddress(mixed $value): string
    {
        if (!is_string($value)) {
            throw new InvalidArgumentException('The value to toChecksumAddress function must be string.');
        }

        $checksumContext = self::prepareChecksumContext($value);

        return '0x' . self::buildChecksumAddress($checksumContext['hash'], $checksumContext['value']);
    }

    /**
     * 准备校验和上下文
     * @return array{hash: string, value: string}
     */
    private static function prepareChecksumContext(string $value): array
    {
        $cleanValue = self::stripZero(strtolower($value));
        $hashResult = self::sha3($cleanValue);

        if (null === $hashResult) {
            throw new InvalidArgumentException('Unable to generate hash for address');
        }

        return [
            'hash' => self::stripZero($hashResult),
            'value' => $cleanValue,
        ];
    }

    /**
     * 构建校验和地址
     */
    private static function buildChecksumAddress(string $hash, string $value): string
    {
        $result = '';

        for ($i = 0; $i < 40; ++$i) {
            if (intval($hash[$i], 16) >= 8) {
                $result .= strtoupper($value[$i]);
            } else {
                $result .= $value[$i];
            }
        }

        return $result;
    }

    /**
     * 检查是否为十六进制字符串
     */
    public static function isHex(mixed $value): bool
    {
        return is_string($value) && 1 === preg_match('/^(0x)?[a-f0-9]*$/i', $value);
    }

    /**
     * SHA3 哈希
     */
    public static function sha3(mixed $value): ?string
    {
        if (!is_string($value)) {
            throw new InvalidArgumentException('The value to sha3 function must be string.');
        }

        if (str_starts_with($value, '0x')) {
            $value = self::hexToBin($value);
        }

        $hash = Keccak::hash($value, 256);

        return self::SHA3_NULL_HASH === $hash ? null : '0x' . $hash;
    }

    /**
     * 转换为字符串
     */
    public static function toString(mixed $value): string
    {
        if (is_array($value)) {
            return '';
        }

        if (is_object($value)) {
            return method_exists($value, '__toString') ? (string) $value : '';
        }

        return (string) $value;
    }

    /**
     * 转换为 Wei
     */
    public static function toWei(mixed $number, mixed $unit): BigNumber
    {
        self::validateToWeiParameters($number, $unit);
        $bn = self::toBn($number);
        $unitMultiplier = new BigNumber(self::UNITS[$unit]);

        return self::calculateWeiValue($bn, $unitMultiplier, $unit);
    }

    /**
     * 计算Wei值
     * @param BigNumber|array{0: BigNumber, 1: BigNumber, 2: int, 3: BigNumber|false} $bn
     */
    private static function calculateWeiValue(BigNumber|array $bn, BigNumber $unitMultiplier, string $unit): BigNumber
    {
        if (is_array($bn)) {
            return self::processFractionalToWei($bn, $unitMultiplier, $unit);
        }

        return $bn->multiply($unitMultiplier);
    }

    /**
     * 验证 toWei 参数
     */
    private static function validateToWeiParameters(mixed $number, mixed $unit): void
    {
        if (!is_string($number) && !($number instanceof BigNumber)) {
            throw new InvalidArgumentException('toWei number must be string or bignumber.');
        }

        if (!is_string($unit)) {
            throw new InvalidArgumentException('toWei unit must be string.');
        }

        if (!isset(self::UNITS[$unit])) {
            throw new InvalidArgumentException("toWei doesn't support {$unit} unit.");
        }
    }

    /**
     * 处理分数转 Wei
     * @param array{0: BigNumber, 1: BigNumber, 2: int, 3: BigNumber|false} $fractionComponents
     */
    private static function processFractionalToWei(array $fractionComponents, BigNumber $unitMultiplier, string $unit): BigNumber
    {
        [$whole, $fraction, $fractionLength, $negative1] = $fractionComponents;

        if ($fractionLength > strlen(self::UNITS[$unit])) {
            throw new InvalidArgumentException('toWei fraction part is out of limit.');
        }

        $wholePart = $whole->multiply($unitMultiplier);
        $fractionPart = self::calculateFractionPart($fraction, $unitMultiplier, $fractionLength);
        $result = $wholePart->add($fractionPart);

        return (false !== $negative1) ? $result->multiply($negative1) : $result;
    }

    /**
     * 计算分数部分
     */
    private static function calculateFractionPart(BigNumber $fraction, BigNumber $unitMultiplier, int $fractionLength): BigNumber
    {
        $base = new BigNumber(self::calculatePowerBase($fractionLength));

        return $fraction->multiply($unitMultiplier)->divide($base)[0];
    }

    /**
     * 计算幂底数
     */
    private static function calculatePowerBase(int $fractionLength): string
    {
        return (string) pow(10, $fractionLength);
    }

    /**
     * 转换为 Ether
     * @return array{0: BigNumber, 1: BigNumber}
     */
    public static function toEther(mixed $number, mixed $unit): array
    {
        $wei = self::toWei($number, $unit);
        $etherUnit = new BigNumber(self::UNITS['ether']);
        $result = $wei->divide($etherUnit);

        return [$result[0], $result[1]];
    }

    /**
     * 从 Wei 转换
     * @return array{0: BigNumber, 1: BigNumber}
     */
    public static function fromWei(mixed $number, mixed $unit): array
    {
        $bn = self::toBn($number);
        if (is_array($bn)) {
            throw new InvalidArgumentException('fromWei does not support fractional numbers directly.');
        }

        if (!is_string($unit)) {
            throw new InvalidArgumentException('fromWei unit must be string.');
        }

        if (!isset(self::UNITS[$unit])) {
            throw new InvalidArgumentException("fromWei doesn't support {$unit} unit.");
        }

        $unitValue = new BigNumber(self::UNITS[$unit]);
        $result = $bn->divide($unitValue);

        return [$result[0], $result[1]];
    }

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

    /**
     * 转换为大数
     * @return BigNumber|array{0: BigNumber, 1: BigNumber, 2: int, 3: BigNumber|false}
     */
    public static function toBn(mixed $number): BigNumber|array
    {
        self::validateToBnInput($number);

        if ($number instanceof BigNumber) {
            return $number;
        }

        if (is_int($number)) {
            return new BigNumber($number);
        }

        if (is_numeric($number)) {
            return self::processNumericString((string) $number);
        }

        if (is_string($number)) {
            return self::processNonNumericString($number);
        }

        throw new InvalidArgumentException('toBn number must be BigNumber, string or int.');
    }

    /**
     * 验证toBn输入
     */
    private static function validateToBnInput(mixed $number): void
    {
        if (!($number instanceof BigNumber)
            && !is_int($number)
            && !is_numeric($number)
            && !is_string($number)) {
            throw new InvalidArgumentException('toBn number must be BigNumber, string or int.');
        }
    }

    /**
     * 处理数值字符串
     * @return BigNumber|array{0: BigNumber, 1: BigNumber, 2: int, 3: BigNumber|false}
     */
    private static function processNumericString(string $number): BigNumber|array
    {
        $isNegative = self::isNegative($number);
        $cleanNumber = $isNegative ? ltrim($number, '-') : $number;

        if (str_contains($cleanNumber, '.')) {
            return self::createFractionalArray($cleanNumber, $isNegative);
        }

        return self::createIntegerBigNumber($cleanNumber, $isNegative);
    }

    /**
     * 创建整数大数
     */
    private static function createIntegerBigNumber(string $number, bool $isNegative): BigNumber
    {
        $bn = new BigNumber($number);

        return $isNegative ? $bn->multiply(new BigNumber(-1)) : $bn;
    }

    /**
     * 创建分数数组
     * @return array{0: BigNumber, 1: BigNumber, 2: int, 3: BigNumber|false}
     */
    private static function createFractionalArray(string $number, bool $isNegative): array
    {
        $parts = explode('.', $number);
        if (2 !== count($parts)) {
            throw new InvalidArgumentException('toBn number must be a valid number.');
        }

        return [
            new BigNumber($parts[0]),
            new BigNumber($parts[1]),
            strlen($parts[1]),
            $isNegative ? new BigNumber(-1) : false,
        ];
    }

    /**
     * 处理非数值字符串
     */
    private static function processNonNumericString(string $number): BigNumber
    {
        $cleanedNumber = self::cleanString($number);

        if ('' === $cleanedNumber) {
            return new BigNumber(0);
        }

        $isNegative = self::isNegative($number);
        $bn = self::parseNumberString($cleanedNumber);

        return $isNegative ? $bn->multiply(new BigNumber(-1)) : $bn;
    }

    /**
     * 清理字符串
     */
    private static function cleanString(string $number): string
    {
        $number = mb_strtolower($number);
        $isNegative = self::isNegative($number);

        return $isNegative ? ltrim($number, '-') : $number;
    }

    /**
     * 解析数字字符串
     */
    private static function parseNumberString(string $number): BigNumber
    {
        if (self::isZeroPrefixed($number)) {
            return self::createBigNumberFromHex(self::stripZero($number));
        }

        if (is_numeric($number)) {
            return new BigNumber($number);
        }

        if (str_contains($number, '.')) {
            throw new InvalidArgumentException('toBn number must be a valid number.');
        }

        if (1 === preg_match('/^[0-9a-f]+$/i', $number)) {
            return self::createBigNumberFromHex($number);
        }

        throw new InvalidArgumentException('toBn number must be valid hex string.');
    }

    /**
     * 从十六进制创建大数
     */
    private static function createBigNumberFromHex(string $hexString): BigNumber
    {
        try {
            return new BigNumber($hexString, 16);
        } catch (\ValueError) {
            throw new InvalidArgumentException('toBn number must be valid hex string.');
        }
    }
}
