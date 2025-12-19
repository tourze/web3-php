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
 * 以太坊地址工具类
 */
class AddressUtils
{
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
     * 检查是否为简单情况(全大写或全小写)
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
        $cleanValue = HexUtils::stripZero($value);
        $hashResult = Utils::sha3(mb_strtolower($cleanValue));

        if (null === $hashResult) {
            return null;
        }

        return [
            'hash' => HexUtils::stripZero($hashResult),
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
        $cleanValue = HexUtils::stripZero(strtolower($value));
        $hashResult = Utils::sha3($cleanValue);

        if (null === $hashResult) {
            throw new InvalidArgumentException('Unable to generate hash for address');
        }

        return [
            'hash' => HexUtils::stripZero($hashResult),
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
}
