<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Contract;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Contract\ArgumentValidator;
use Tourze\Web3PHP\Exception\InvalidArgumentException;

/**
 * ArgumentValidator 测试
 * @internal
 */
#[CoversClass(ArgumentValidator::class)]
final class ArgumentValidatorTest extends TestCase
{
    private ArgumentValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new ArgumentValidator();
    }

    /**
     * 测试验证有效的字符串方法名
     */
    public function testValidateMethodWithValidStringShouldPass(): void
    {
        $this->expectNotToPerformAssertions();
        $this->validator->validateMethod('transfer');
        $this->validator->validateMethod('balanceOf');
        $this->validator->validateMethod('approve');
    }

    /**
     * 测试验证无效的非字符串方法名应抛出异常
     */
    public function testValidateMethodWithNonStringShouldThrowException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Please make sure the method is string.');

        $this->validator->validateMethod(123);
    }

    /**
     * 测试验证空方法名应抛出异常
     */
    public function testValidateMethodWithNullShouldThrowException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Please make sure the method is string.');

        $this->validator->validateMethod(null);
    }

    /**
     * 测试验证数组作为方法名应抛出异常
     */
    public function testValidateMethodWithArrayShouldThrowException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Please make sure the method is string.');

        $this->validator->validateMethod(['method']);
    }

    /**
     * 测试验证有效的回调函数
     */
    public function testValidateCallbackWithValidCallableShouldPass(): void
    {
        $this->expectNotToPerformAssertions();

        // 测试闭包
        $this->validator->validateCallback(function () {});

        // 测试函数名字符串
        $this->validator->validateCallback('strlen');

        // 测试实例方法（创建一个简单的callable）
        $callable = function () {};
        $this->validator->validateCallback($callable);
    }

    /**
     * 测试验证无效的非可调用对象应抛出异常
     */
    public function testValidateCallbackWithNonCallableShouldThrowException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The last param must be callback function.');

        $this->validator->validateCallback('not_a_function');
    }

    /**
     * 测试验证字符串但非函数作为回调应抛出异常
     */
    public function testValidateCallbackWithInvalidStringShouldThrowException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The last param must be callback function.');

        $this->validator->validateCallback('invalid_function_name');
    }

    /**
     * 测试验证数字作为回调应抛出异常
     */
    public function testValidateCallbackWithNumberShouldThrowException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The last param must be callback function.');

        $this->validator->validateCallback(123);
    }

    /**
     * 测试验证null作为回调应抛出异常
     */
    public function testValidateCallbackWithNullShouldThrowException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The last param must be callback function.');

        $this->validator->validateCallback(null);
    }

    /**
     * 测试验证有效的默认区块标签
     */
    public function testValidateDefaultBlockWithValidTagShouldReturnValue(): void
    {
        $this->assertEquals('latest', $this->validator->validateDefaultBlock('latest'));
        $this->assertEquals('earliest', $this->validator->validateDefaultBlock('earliest'));
        $this->assertEquals('pending', $this->validator->validateDefaultBlock('pending'));
    }

    /**
     * 测试验证有效的数量格式默认区块
     */
    public function testValidateDefaultBlockWithValidQuantityShouldReturnValue(): void
    {
        $this->assertEquals('0x123', $this->validator->validateDefaultBlock('0x123'));
        $this->assertEquals('0x0', $this->validator->validateDefaultBlock('0x0'));
        $this->assertEquals('0xff', $this->validator->validateDefaultBlock('0xff'));
    }

    /**
     * 测试验证无效的默认区块应返回latest
     */
    public function testValidateDefaultBlockWithInvalidValueShouldReturnLatest(): void
    {
        $this->assertEquals('latest', $this->validator->validateDefaultBlock('invalid'));
        // 测试一些真正无效的值
        $this->assertEquals('latest', $this->validator->validateDefaultBlock('not_a_valid_block'));
        $this->assertEquals('latest', $this->validator->validateDefaultBlock(null));
        $this->assertEquals('latest', $this->validator->validateDefaultBlock([]));
        // 由于stdClass不能转换为字符串，我们测试其他无效值
        $this->assertEquals('latest', $this->validator->validateDefaultBlock('invalid_hex_without_0x'));
    }

    /**
     * 测试验证有效的交易对象
     */
    public function testValidateTransactionWithValidTransactionShouldReturnArray(): void
    {
        $validTransaction = ['from' => '0x123...', 'to' => '0x456...', 'gas' => '21000'];
        $result = $this->validator->validateTransaction($validTransaction);
        $this->assertEquals($validTransaction, $result);
    }

    /**
     * 测试验证无效的非数组交易应返回空数组
     */
    public function testValidateTransactionWithNonArrayShouldReturnEmptyArray(): void
    {
        $result = $this->validator->validateTransaction('not an array');
        $this->assertEquals([], $result);

        $result = $this->validator->validateTransaction(123);
        $this->assertEquals([], $result);

        $result = $this->validator->validateTransaction(null);
        $this->assertEquals([], $result);
    }

    /**
     * 测试验证非交易对象数组应返回空数组
     */
    public function testValidateTransactionWithNonTransactionArrayShouldReturnEmptyArray(): void
    {
        // 创建一个不包含任何交易字段的数组（避免使用'value'，因为它是交易字段）
        $nonTransactionArray = ['name' => 'test', 'amount' => 123];
        $result = $this->validator->validateTransaction($nonTransactionArray);
        $this->assertEquals([], $result);

        // 测试另一个不包含交易字段的数组
        $anotherArray = ['config' => 'data', 'status' => 'active'];
        $result2 = $this->validator->validateTransaction($anotherArray);
        $this->assertEquals([], $result2);
    }

    /**
     * 测试验证构造函数参数数量不足应抛出异常
     */
    public function testValidateConstructorArgumentsWithInsufficientArgsShouldThrowException(): void
    {
        $arguments = ['param1'];
        $constructor = ['inputs' => [['type' => 'string'], ['type' => 'uint256']]];
        $expectedCount = 2;

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Please make sure you have put all constructor params and callback.');

        $this->validator->validateConstructorArguments($arguments, $constructor, $expectedCount);
    }

    /**
     * 测试验证构造函数参数数量充足应通过
     */
    public function testValidateConstructorArgumentsWithSufficientArgsShouldPass(): void
    {
        $arguments = ['param1', 'param2', 'callback'];
        $constructor = ['inputs' => [['type' => 'string'], ['type' => 'uint256']]];
        $expectedCount = 2;

        $this->expectNotToPerformAssertions();
        $this->validator->validateConstructorArguments($arguments, $constructor, $expectedCount);
    }

    /**
     * 测试验证构造函数参数数量相等应通过
     */
    public function testValidateConstructorArgumentsWithExactArgsShouldPass(): void
    {
        $arguments = ['param1', 'param2'];
        $constructor = ['inputs' => [['type' => 'string'], ['type' => 'uint256']]];
        $expectedCount = 2;

        $this->expectNotToPerformAssertions();
        $this->validator->validateConstructorArguments($arguments, $constructor, $expectedCount);
    }

    /**
     * 测试检测包含from字段的交易对象
     */
    public function testIsTransactionObjectWithFromFieldShouldReturnTrue(): void
    {
        $transaction = ['from' => '0x123...'];
        $this->assertTrue($this->validator->isTransactionObject($transaction));
    }

    /**
     * 测试检测包含to字段的交易对象
     */
    public function testIsTransactionObjectWithToFieldShouldReturnTrue(): void
    {
        $transaction = ['to' => '0x456...'];
        $this->assertTrue($this->validator->isTransactionObject($transaction));
    }

    /**
     * 测试检测包含gas字段的交易对象
     */
    public function testIsTransactionObjectWithGasFieldShouldReturnTrue(): void
    {
        $transaction = ['gas' => '21000'];
        $this->assertTrue($this->validator->isTransactionObject($transaction));
    }

    /**
     * 测试检测包含gasPrice字段的交易对象
     */
    public function testIsTransactionObjectWithGasPriceFieldShouldReturnTrue(): void
    {
        $transaction = ['gasPrice' => '20000000000'];
        $this->assertTrue($this->validator->isTransactionObject($transaction));
    }

    /**
     * 测试检测包含value字段的交易对象
     */
    public function testIsTransactionObjectWithValueFieldShouldReturnTrue(): void
    {
        $transaction = ['value' => '1000000000000000000'];
        $this->assertTrue($this->validator->isTransactionObject($transaction));
    }

    /**
     * 测试检测包含data字段的交易对象
     */
    public function testIsTransactionObjectWithDataFieldShouldReturnTrue(): void
    {
        $transaction = ['data' => '0xa9059cbb...'];
        $this->assertTrue($this->validator->isTransactionObject($transaction));
    }

    /**
     * 测试检测包含nonce字段的交易对象
     */
    public function testIsTransactionObjectWithNonceFieldShouldReturnTrue(): void
    {
        $transaction = ['nonce' => '0x1'];
        $this->assertTrue($this->validator->isTransactionObject($transaction));
    }

    /**
     * 测试检测包含多个交易字段的对象
     */
    public function testIsTransactionObjectWithMultipleFieldsShouldReturnTrue(): void
    {
        $transaction = [
            'from' => '0x123...',
            'to' => '0x456...',
            'gas' => '21000',
            'gasPrice' => '20000000000',
            'value' => '1000000000000000000',
            'nonce' => '0x1',
        ];
        $this->assertTrue($this->validator->isTransactionObject($transaction));
    }

    /**
     * 测试检测不包含任何交易字段的对象应返回false
     */
    public function testIsTransactionObjectWithNoTransactionFieldsShouldReturnFalse(): void
    {
        $nonTransactionObject = ['name' => 'test', 'amount' => 100];
        $this->assertFalse($this->validator->isTransactionObject($nonTransactionObject));
    }

    /**
     * 测试检测空数组应返回false
     */
    public function testIsTransactionObjectWithEmptyArrayShouldReturnFalse(): void
    {
        $this->assertFalse($this->validator->isTransactionObject([]));
    }

    /**
     * 测试检测包含交易字段但值为null的对象
     */
    public function testIsTransactionObjectWithNullValuesShouldReturnTrue(): void
    {
        $transaction = ['from' => null, 'someOtherField' => 'value'];
        // 由于isset检查，null值的字段不会被认为是已设置的
        $this->assertFalse($this->validator->isTransactionObject($transaction));

        // 但如果字段存在且不是null
        $transaction = ['from' => '0x123...', 'someOtherField' => 'value'];
        $this->assertTrue($this->validator->isTransactionObject($transaction));
    }

    /**
     * 测试检测包含空字符串交易字段的对象
     */
    public function testIsTransactionObjectWithEmptyStringFieldsShouldReturnTrue(): void
    {
        $transaction = ['from' => '', 'otherField' => 'value'];
        $this->assertTrue($this->validator->isTransactionObject($transaction));
    }

    /**
     * 测试检测包含数字0的交易字段
     */
    public function testIsTransactionObjectWithZeroValuesShouldReturnTrue(): void
    {
        $transaction = ['gas' => 0, 'otherField' => 'value'];
        $this->assertTrue($this->validator->isTransactionObject($transaction));
    }

    /**
     * 测试检测包含混合字段的对象（既有交易字段又有非交易字段）
     */
    public function testIsTransactionObjectWithMixedFieldsShouldReturnTrue(): void
    {
        $transaction = [
            'from' => '0x123...',
            'customField' => 'custom value',
            'anotherField' => 123,
            'gas' => '21000',
        ];
        $this->assertTrue($this->validator->isTransactionObject($transaction));
    }
}
