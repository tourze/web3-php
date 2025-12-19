<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Validators;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Validators\AddressValidator;

/**
 * @internal
 */
#[CoversClass(AddressValidator::class)]
final class AddressValidatorTest extends TestCase
{
    public function testValidateWithValidAddress(): void
    {
        $validAddresses = [
            '0x1234567890123456789012345678901234567890',
            '0x0000000000000000000000000000000000000000',
            '0xffffffffffffffffffffffffffffffffffffffff',
            '0xFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF',
            '0x5aAeb6053F3E94C9b9A09f33669435E7Ef1BeAed',
            '0xabcdefABCDEF0123456789abcdefABCDEF012345',
        ];

        foreach ($validAddresses as $address) {
            $this->assertTrue(
                AddressValidator::validate($address),
                "Address {$address} should be valid"
            );
        }
    }

    public function testValidateWithInvalidAddress(): void
    {
        $invalidAddresses = [
            // Too short
            '0x123456789012345678901234567890123456789',
            '0x12345678901234567890123456789012345678',

            // Too long
            '0x12345678901234567890123456789012345678901',
            '0x123456789012345678901234567890123456789012',

            // Missing 0x prefix
            '1234567890123456789012345678901234567890',

            // Invalid characters
            '0x1234567890123456789012345678901234567890g',
            '0x123456789012345678901234567890123456789G',
            '0x123456789012345678901234567890123456789!',
            '0x123456789012345678901234567890123456789@',

            // Wrong prefix
            '1x1234567890123456789012345678901234567890',
            '0X1234567890123456789012345678901234567890', // Capital X not valid in this validator

            // Empty string
            '',

            // Just prefix
            '0x',

            // Spaces
            '0x1234567890123456789012345678901234567890 ',
            ' 0x1234567890123456789012345678901234567890',
        ];

        foreach ($invalidAddresses as $address) {
            $this->assertFalse(
                AddressValidator::validate($address),
                "Address {$address} should be invalid"
            );
        }
    }

    public function testValidateWithNonStringValue(): void
    {
        $nonStringValues = [
            123,
            123.45,
            true,
            false,
            null,
            [],
            new \stdClass(),
        ];

        foreach ($nonStringValues as $value) {
            $this->assertFalse(
                AddressValidator::validate($value),
                'Non-string value should be invalid'
            );
        }
    }

    public function testValidateAcceptsBothLowerAndUpperCase(): void
    {
        $lowerCaseAddress = '0xabcdef1234567890abcdef1234567890abcdef12';
        $upperCaseAddress = '0xABCDEF1234567890ABCDEF1234567890ABCDEF12';
        $mixedCaseAddress = '0xAbCdEf1234567890aBcDeF1234567890AbCdEf12';

        $this->assertTrue(AddressValidator::validate($lowerCaseAddress));
        $this->assertTrue(AddressValidator::validate($upperCaseAddress));
        $this->assertTrue(AddressValidator::validate($mixedCaseAddress));
    }

    public function testValidateWithExactLength(): void
    {
        // Exactly 40 hex characters after 0x
        $exactLength = '0x' . str_repeat('a', 40);
        $this->assertTrue(AddressValidator::validate($exactLength));

        // One character short
        $tooShort = '0x' . str_repeat('a', 39);
        $this->assertFalse(AddressValidator::validate($tooShort));

        // One character too long
        $tooLong = '0x' . str_repeat('a', 41);
        $this->assertFalse(AddressValidator::validate($tooLong));
    }

    public function testValidateWithAllValidHexCharacters(): void
    {
        // Test all valid hex characters
        $validHexChars = '0123456789abcdefABCDEF';
        $address = '0x' . str_repeat('0', 40);

        // Replace each position with each valid hex character
        for ($i = 0; $i < strlen($validHexChars); ++$i) {
            $char = $validHexChars[$i];
            $testAddress = '0x' . $char . str_repeat('0', 39);
            $this->assertTrue(
                AddressValidator::validate($testAddress),
                "Address with character {$char} should be valid"
            );
        }
    }

    public function testValidateWithInvalidHexCharacters(): void
    {
        $invalidChars = 'ghijklmnopqrstuvwxyzGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()';

        for ($i = 0; $i < strlen($invalidChars); ++$i) {
            $char = $invalidChars[$i];
            $testAddress = '0x' . $char . str_repeat('0', 39);
            $this->assertFalse(
                AddressValidator::validate($testAddress),
                "Address with invalid character {$char} should be invalid"
            );
        }
    }

    public function testValidateIsConsistent(): void
    {
        $address = '0x1234567890123456789012345678901234567890';

        // Multiple calls should return same result
        $result1 = AddressValidator::validate($address);
        $result2 = AddressValidator::validate($address);
        $result3 = AddressValidator::validate($address);

        $this->assertSame($result1, $result2);
        $this->assertSame($result2, $result3);
        $this->assertTrue($result1);
    }
}
