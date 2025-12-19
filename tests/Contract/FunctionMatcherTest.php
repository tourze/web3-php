<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Contract;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Contract\FunctionMatcher;
use Tourze\Web3PHP\Contracts\Ethabi;
use Tourze\Web3PHP\Exception\InvalidArgumentException;

/**
 * FunctionMatcher 测试
 * @internal
 */
#[CoversClass(FunctionMatcher::class)]
final class FunctionMatcherTest extends TestCase
{
    private FunctionMatcher $matcher;

    private Ethabi $ethabi;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ethabi = new class extends Ethabi {
            /** @var string|null */
            private ?string $nextReturnValue = null;

            /** @var \Throwable|null */
            private ?\Throwable $nextException = null;

            public function setNextReturnValue(string $value): void
            {
                $this->nextReturnValue = $value;
            }

            public function setNextException(\Throwable $exception): void
            {
                $this->nextException = $exception;
            }

            public function encodeParameters(mixed $function, mixed $params): string
            {
                if (null !== $this->nextException) {
                    try {
                        throw $this->nextException;
                    } finally {
                        $this->nextException = null;
                    }
                }

                if (null !== $this->nextReturnValue) {
                    try {
                        return $this->nextReturnValue;
                    } finally {
                        $this->nextReturnValue = null;
                    }
                }

                return 'default_encoded_data';
            }
        };
        $this->matcher = new FunctionMatcher($this->ethabi);
    }

    /**
     * 辅助方法：设置模拟返回值
     */
    private function setMockReturnValue(string $value): void
    {
        if (method_exists($this->ethabi, 'setNextReturnValue')) {
            $this->ethabi->setNextReturnValue($value);
        }
    }

    /**
     * 辅助方法：设置模拟异常
     */
    private function setMockException(\Throwable $exception): void
    {
        if (method_exists($this->ethabi, 'setNextException')) {
            $this->ethabi->setNextException($exception);
        }
    }

    /**
     * 测试查找匹配函数成功的情况
     */
    public function testFindMatchingFunctionWithValidParamsShouldReturnMatchedFunction(): void
    {
        $functions = [
            'transfer' => [
                'name' => 'transfer',
                'type' => 'function',
                'inputs' => [
                    ['type' => 'address', 'name' => 'to'],
                    ['type' => 'uint256', 'name' => 'amount'],
                ],
            ],
        ];
        $methodName = 'transfer';
        $params = ['0x123...', '1000'];
        $encodedData = '0xa9059cbb...';
        $functionName = 'transfer(address,uint256)';

        $this->setMockReturnValue($encodedData);

        $result = $this->matcher->findMatchingFunction($functions, $methodName, $params);

        $this->assertEquals($functions['transfer'], $result['function']);
        $this->assertEquals($encodedData, $result['encodedData']);
        $this->assertIsString($result['functionName']);
    }

    /**
     * 测试查找匹配函数参数数量不匹配的情况
     */
    public function testFindMatchingFunctionWithMismatchedParamCountShouldThrowException(): void
    {
        $functions = [
            'transfer' => [
                'name' => 'transfer',
                'type' => 'function',
                'inputs' => [
                    ['type' => 'address', 'name' => 'to'],
                    ['type' => 'uint256', 'name' => 'amount'],
                ],
            ],
        ];
        $methodName = 'transfer';
        $params = ['0x123...']; // 只有一个参数，但函数需要两个

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Please make sure you have put all function params and callback.');

        $this->matcher->findMatchingFunction($functions, $methodName, $params);
    }

    /**
     * 测试查找不存在的方法应抛出异常
     */
    public function testFindMatchingFunctionWithNonExistentMethodShouldThrowException(): void
    {
        $functions = [
            'transfer' => [
                'name' => 'transfer',
                'type' => 'function',
                'inputs' => [
                    ['type' => 'address', 'name' => 'to'],
                ],
            ],
        ];
        $methodName = 'nonExistentMethod';
        $params = ['0x123...'];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Please make sure the method exists.');

        $this->matcher->findMatchingFunction($functions, $methodName, $params);
    }

    /**
     * 测试查找匹配函数编码失败的情况
     */
    public function testFindMatchingFunctionWithEncodingErrorShouldThrowException(): void
    {
        $functions = [
            'transfer' => [
                'name' => 'transfer',
                'type' => 'function',
                'inputs' => [
                    ['type' => 'address', 'name' => 'to'],
                    ['type' => 'uint256', 'name' => 'amount'],
                ],
            ],
        ];
        $methodName = 'transfer';
        $params = ['invalid_address', 'invalid_amount'];

        $this->setMockException(new \InvalidArgumentException('Encoding failed'));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Please make sure you have put all function params and callback.');

        $this->matcher->findMatchingFunction($functions, $methodName, $params);
    }

    /**
     * 测试查找匹配的调用函数成功的情况
     */
    public function testFindMatchingCallFunctionWithValidArgsShouldReturnMatchedFunction(): void
    {
        $functions = [
            'balanceOf' => [
                'name' => 'balanceOf',
                'type' => 'function',
                'inputs' => [
                    ['type' => 'address', 'name' => 'owner'],
                ],
            ],
        ];
        $method = 'balanceOf';
        $arguments = ['0x123...', 'additional_arg'];
        $expectedParams = ['0x123...'];

        $this->setMockReturnValue('encoded_data');

        $result = $this->matcher->findMatchingCallFunction($functions, $method, $arguments);

        $this->assertEquals($expectedParams, $result['params']);
        $this->assertEquals($functions['balanceOf'], $result['function']);
        $this->assertEquals(['additional_arg'], $result['remainingArguments']); // 应该包含剩余的参数
    }

    /**
     * 测试查找匹配的调用函数参数不足的情况
     */
    public function testFindMatchingCallFunctionWithInsufficientArgsShouldThrowException(): void
    {
        $functions = [
            'transfer' => [
                'name' => 'transfer',
                'type' => 'function',
                'inputs' => [
                    ['type' => 'address', 'name' => 'to'],
                    ['type' => 'uint256', 'name' => 'amount'],
                ],
            ],
        ];
        $method = 'transfer';
        $arguments = ['0x123...']; // 只有一个参数，但函数需要两个

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Please make sure you have put all function params and callback.');

        $this->matcher->findMatchingCallFunction($functions, $method, $arguments);
    }

    /**
     * 测试查找匹配的调用函数不存在的情况
     */
    public function testFindMatchingCallFunctionWithNonExistentMethodShouldThrowException(): void
    {
        $functions = [
            'transfer' => [
                'name' => 'transfer',
                'type' => 'function',
                'inputs' => [],
            ],
        ];
        $method = 'nonExistentMethod';
        $arguments = [];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Please make sure the method exists.');

        $this->matcher->findMatchingCallFunction($functions, $method, $arguments);
    }

    /**
     * 测试查找匹配的调用函数编码失败的情况
     */
    public function testFindMatchingCallFunctionWithEncodingErrorShouldThrowException(): void
    {
        $functions = [
            'balanceOf' => [
                'name' => 'balanceOf',
                'type' => 'function',
                'inputs' => [
                    ['type' => 'address', 'name' => 'owner'],
                ],
            ],
        ];
        $method = 'balanceOf';
        $arguments = ['invalid_address'];

        $this->setMockException(new \InvalidArgumentException('Encoding failed'));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Please make sure you have put all function params and callback.');

        $this->matcher->findMatchingCallFunction($functions, $method, $arguments);
    }

    /**
     * 测试查找函数重载情况（同名但参数不同）
     */
    public function testFindMatchingFunctionWithOverloadedFunctionsShouldReturnCorrectOne(): void
    {
        $functions = [
            'transfer' => [
                'name' => 'transfer',
                'type' => 'function',
                'inputs' => [
                    ['type' => 'address', 'name' => 'to'],
                    ['type' => 'uint256', 'name' => 'amount'],
                ],
            ],
            'transferFrom' => [
                'name' => 'transfer',
                'type' => 'function',
                'inputs' => [
                    ['type' => 'address', 'name' => 'from'],
                    ['type' => 'address', 'name' => 'to'],
                    ['type' => 'uint256', 'name' => 'amount'],
                ],
            ],
        ];
        $methodName = 'transfer';
        $params = ['0x123...', '1000'];
        $encodedData = '0xa9059cbb...';

        // 第一次调用应该匹配两参数的函数
        $this->setMockReturnValue($encodedData);

        $result = $this->matcher->findMatchingFunction($functions, $methodName, $params);

        $this->assertEquals($functions['transfer'], $result['function']);
        $this->assertEquals($encodedData, $result['encodedData']);
    }

    /**
     * 测试查找匹配函数空参数列表的情况
     */
    public function testFindMatchingFunctionWithEmptyParamsShouldReturnNoParamFunction(): void
    {
        $functions = [
            'totalSupply' => [
                'name' => 'totalSupply',
                'type' => 'function',
                'inputs' => [],
            ],
        ];
        $methodName = 'totalSupply';
        $params = [];
        $encodedData = '0x18160ddd';

        $this->setMockReturnValue($encodedData);

        $result = $this->matcher->findMatchingFunction($functions, $methodName, $params);

        $this->assertEquals($functions['totalSupply'], $result['function']);
        $this->assertEquals($encodedData, $result['encodedData']);
    }

    /**
     * 测试查找匹配调用函数空参数列表的情况
     */
    public function testFindMatchingCallFunctionWithEmptyParamsShouldReturnNoParamFunction(): void
    {
        $functions = [
            'name' => [
                'name' => 'name',
                'type' => 'function',
                'inputs' => [],
            ],
        ];
        $method = 'name';
        $arguments = ['extra_arg'];

        $this->setMockReturnValue('encoded_data');

        $result = $this->matcher->findMatchingCallFunction($functions, $method, $arguments);

        $this->assertEquals([], $result['params']);
        $this->assertEquals($functions['name'], $result['function']);
        $this->assertEquals(['extra_arg'], $arguments);
    }

    /**
     * 测试多个函数同名但参数数量不同时的匹配
     */
    public function testFindMatchingFunctionWithDifferentParamCountsShouldMatchCorrectOne(): void
    {
        // 模拟函数重载的情况，但由于数组键相同，实际上只会有一个函数
        // 这个测试主要验证参数数量匹配逻辑
        $functions = [
            'mint' => [
                'name' => 'mint',
                'type' => 'function',
                'inputs' => [
                    ['type' => 'uint256', 'name' => 'amount'],
                ],
            ],
        ];
        $methodName = 'mint';
        $params = ['1000'];
        $encodedData = '0xa0712d68';

        $this->setMockReturnValue($encodedData);

        $result = $this->matcher->findMatchingFunction($functions, $methodName, $params);

        $this->assertEquals($functions['mint'], $result['function']);
        $this->assertEquals($encodedData, $result['encodedData']);
    }

    /**
     * 测试查找匹配调用函数参数过多的情况
     */
    public function testFindMatchingCallFunctionWithExtraArgsShouldReturnCorrectResult(): void
    {
        $functions = [
            'approve' => [
                'name' => 'approve',
                'type' => 'function',
                'inputs' => [
                    ['type' => 'address', 'name' => 'spender'],
                    ['type' => 'uint256', 'name' => 'amount'],
                ],
            ],
        ];
        $method = 'approve';
        $arguments = ['0x123...', '1000', 'extra1', 'extra2'];
        $expectedParams = ['0x123...', '1000'];

        $this->setMockReturnValue('encoded_data');

        $result = $this->matcher->findMatchingCallFunction($functions, $method, $arguments);

        $this->assertEquals($expectedParams, $result['params']);
        $this->assertEquals($functions['approve'], $result['function']);
        $this->assertEquals(['extra1', 'extra2'], $result['remainingArguments']); // 剩余参数
    }

    /**
     * 测试函数列表为空的情况
     */
    public function testFindMatchingFunctionWithEmptyFunctionListShouldThrowException(): void
    {
        $functions = [];
        $methodName = 'transfer';
        $params = ['0x123...', '1000'];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Please make sure the method exists.');

        $this->matcher->findMatchingFunction($functions, $methodName, $params);
    }

    /**
     * 测试查找匹配调用函数列表为空的情况
     */
    public function testFindMatchingCallFunctionWithEmptyFunctionListShouldThrowException(): void
    {
        $functions = [];
        $method = 'balanceOf';
        $arguments = ['0x123...'];

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Please make sure the method exists.');

        $this->matcher->findMatchingCallFunction($functions, $method, $arguments);
    }
}
