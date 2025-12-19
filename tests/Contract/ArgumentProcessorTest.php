<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Contract;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Contract\ArgumentProcessor;
use Tourze\Web3PHP\Contract\ArgumentValidator;
use Tourze\Web3PHP\Exception\InvalidArgumentException;

/**
 * ArgumentProcessor 测试
 * @internal
 */
#[CoversClass(ArgumentProcessor::class)]
final class ArgumentProcessorTest extends TestCase
{
    private ArgumentProcessor $processor;

    /** @var ArgumentValidator */
    private ArgumentValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = $this->createMockValidator();
        $this->processor = new ArgumentProcessor($this->validator);
    }

    /**
     * 创建模拟验证器，简化setUp方法的复杂度
     */
    private function createMockValidator(): ArgumentValidator
    {
        return new class extends ArgumentValidator {
            /** @var array<mixed> */
            private array $returnValues = [];

            private ?\Throwable $nextException = null;

            public function setNextReturnValue(mixed $value): void
            {
                $this->returnValues[] = $value;
            }

            public function setNextException(\Throwable $exception): void
            {
                $this->nextException = $exception;
            }

            private function getNextReturnValue(): mixed
            {
                return count($this->returnValues) > 0 ? array_shift($this->returnValues) : null;
            }

            public function validateMethod($method): void
            {
                $this->throwExceptionIfNeeded();
            }

            public function validateCallback($callback): void
            {
                $this->throwExceptionIfNeeded();
            }

            public function isTransactionObject(array $data): bool
            {
                $this->throwExceptionIfNeeded();
                $value = $this->getNextReturnValue();

                return null !== $value ? (bool) $value : false;
            }

            public function validateTransaction(mixed $transaction): array
            {
                $this->throwExceptionIfNeeded();
                $value = $this->getNextReturnValue();

                return null !== $value && is_array($value) ? $value : (is_array($transaction) ? $transaction : []);
            }

            public function validateDefaultBlock(mixed $block): mixed
            {
                $this->throwExceptionIfNeeded();
                $value = $this->getNextReturnValue();

                return null !== $value ? $value : $block;
            }

            public function validateConstructorArguments(array $arguments, array $constructor, int $expectedCount): void
            {
                $this->throwExceptionIfNeeded();
            }

            /**
             * 统一的异常处理逻辑，减少重复代码
             */
            private function throwExceptionIfNeeded(): void
            {
                if (null !== $this->nextException) {
                    try {
                        throw $this->nextException;
                    } finally {
                        $this->nextException = null;
                    }
                }
            }
        };
    }

    /**
     * 辅助方法：调用模拟验证器方法
     * @param mixed $value
     */
    private function setMockReturnValue(mixed $value): void
    {
        if (method_exists($this->validator, 'setNextReturnValue')) {
            $this->validator->setNextReturnValue($value);
        }
    }

    /**
     * 辅助方法：设置模拟异常
     */
    private function setMockException(\Throwable $exception): void
    {
        if (method_exists($this->validator, 'setNextException')) {
            $this->validator->setNextException($exception);
        }
    }

    /**
     * 测试处理包含交易对象的函数参数
     */
    public function testProcessFunctionArgumentsWithTransactionShouldReturnProcessedData(): void
    {
        $method = 'transfer';
        $params = ['0x123...', '1000'];
        $transaction = ['from' => '0xabc...', 'gas' => '21000'];
        $callback = function () {};
        $arguments = [$method, ...$params, $transaction, $callback];

        $this->setMockReturnValue(true);

        $result = $this->processor->processFunctionArguments($arguments);

        $this->assertEquals($method, $result['method']);
        $this->assertEquals($params, $result['params']);
        $this->assertEquals($callback, $result['callback']);
        $this->assertTrue($result['hasTransaction']);
        $this->assertEquals($transaction, $result['transaction']);
    }

    /**
     * 测试处理不包含交易对象的函数参数
     */
    public function testProcessFunctionArgumentsWithoutTransactionShouldReturnProcessedData(): void
    {
        $method = 'balanceOf';
        $params = ['0x123...'];
        $callback = function () {};
        $arguments = [$method, ...$params, $callback];

        // Mock validation calls are handled by anonymous class

        $result = $this->processor->processFunctionArguments($arguments);

        $this->assertEquals($method, $result['method']);
        $this->assertEquals($params, $result['params']);
        $this->assertEquals($callback, $result['callback']);
        $this->assertFalse($result['hasTransaction']);
        $this->assertEmpty($result['transaction']);
    }

    /**
     * 测试处理空参数列表的函数参数
     */
    public function testProcessFunctionArgumentsWithEmptyParamsShouldReturnProcessedData(): void
    {
        $method = 'totalSupply';
        $callback = function () {};
        $arguments = [$method, $callback];

        // Mock validation calls are handled by anonymous class

        $result = $this->processor->processFunctionArguments($arguments);

        $this->assertEquals($method, $result['method']);
        $this->assertEmpty($result['params']);
        $this->assertEquals($callback, $result['callback']);
        $this->assertFalse($result['hasTransaction']);
        $this->assertEmpty($result['transaction']);
    }

    /**
     * 测试处理包含非交易对象最后参数的函数参数
     */
    public function testProcessFunctionArgumentsWithNonTransactionLastArgShouldTreatAsParam(): void
    {
        $method = 'someFunction';
        $params = ['param1', ['some' => 'data']]; // 最后一个参数是数组但不是交易对象
        $callback = function () {};
        $arguments = [$method, ...$params, $callback];

        // Mock validation calls are handled by anonymous class

        $this->setMockReturnValue(false);

        $result = $this->processor->processFunctionArguments($arguments);

        $this->assertEquals($method, $result['method']);
        $this->assertEquals($params, $result['params']);
        $this->assertEquals($callback, $result['callback']);
        $this->assertFalse($result['hasTransaction']);
        $this->assertEmpty($result['transaction']);
    }

    /**
     * 测试处理call方法参数（包含交易和默认区块）
     */
    public function testProcessCallArgumentsWithTransactionAndBlockShouldReturnProcessedData(): void
    {
        $method = 'balanceOf';
        $transaction = ['from' => '0xabc...'];
        $defaultBlock = 'latest';
        $callback = function () {};
        $arguments = [$method, $transaction, $defaultBlock, $callback];
        $expectedDefaultBlock = 'latest';

        // Mock validation calls are handled by anonymous class

        // Set up return values for the specific order of calls
        $this->setMockReturnValue($transaction);  // For validateTransaction
        $this->setMockReturnValue($expectedDefaultBlock);  // For validateDefaultBlock

        $result = $this->processor->processCallArguments($arguments, 'pending');

        $this->assertEquals($method, $result['method']);
        $this->assertEquals($callback, $result['callback']);
        $this->assertEquals($transaction, $result['transaction']);
        $this->assertEquals($expectedDefaultBlock, $result['defaultBlock']);
    }

    /**
     * 测试处理call方法参数（只有交易对象）
     */
    public function testProcessCallArgumentsWithOnlyTransactionShouldUseDefaultBlock(): void
    {
        $method = 'totalSupply';
        $transaction = ['from' => '0xabc...'];
        $callback = function () {};
        $arguments = [$method, $transaction, $callback];
        $defaultBlockParam = 'pending';

        // Mock validation calls are handled by anonymous class

        $this->setMockReturnValue(true);

        $result = $this->processor->processCallArguments($arguments, $defaultBlockParam);

        $this->assertEquals($method, $result['method']);
        $this->assertEquals($callback, $result['callback']);
        $this->assertEquals($transaction, $result['transaction']);
        $this->assertEquals($defaultBlockParam, $result['defaultBlock']);
    }

    /**
     * 测试处理call方法参数（只有区块参数）
     */
    public function testProcessCallArgumentsWithOnlyBlockShouldUseEmptyTransaction(): void
    {
        $method = 'name';
        $blockNumber = ['block' => '0x123']; // 改为数组，这样会调用isTransactionObject
        $callback = function () {};
        $arguments = [$method, $blockNumber, $callback];

        // Mock validation calls are handled by anonymous class

        $this->setMockReturnValue(false);
        $this->setMockReturnValue($blockNumber);

        $result = $this->processor->processCallArguments($arguments, 'latest');

        $this->assertEquals($method, $result['method']);
        $this->assertEquals($callback, $result['callback']);
        $this->assertEmpty($result['transaction']);
        $this->assertEquals($blockNumber, $result['defaultBlock']);
    }

    /**
     * 测试处理call方法参数（只有方法名和回调）
     */
    public function testProcessCallArgumentsWithMinimalArgsShouldUseDefaults(): void
    {
        $method = 'symbol';
        $callback = function () {};
        $arguments = [$method, $callback];
        $defaultBlockParam = 'latest';

        // Mock validation calls are handled by anonymous class

        $result = $this->processor->processCallArguments($arguments, $defaultBlockParam);

        $this->assertEquals($method, $result['method']);
        $this->assertEquals($callback, $result['callback']);
        $this->assertEmpty($result['transaction']);
        $this->assertEquals($defaultBlockParam, $result['defaultBlock']);
    }

    /**
     * 测试处理构造函数Gas估算参数（包含交易对象）
     */
    public function testProcessConstructorGasArgumentsWithTransactionShouldReturnProcessedData(): void
    {
        $constructorParams = ['TokenName', 'TKN'];
        $transaction = ['from' => '0xabc...', 'gas' => '3000000'];
        $callback = function () {};
        $arguments = [...$constructorParams, $transaction, $callback];
        $constructor = [
            'type' => 'constructor',
            'inputs' => [
                ['type' => 'string', 'name' => 'name'],
                ['type' => 'string', 'name' => 'symbol'],
            ],
        ];
        $bytecode = '0x608060405234801561001057600080fd5b50...';

        // Mock validation calls are handled by anonymous class

        $result = $this->processor->processConstructorGasArguments($arguments, $constructor, $bytecode);

        $this->assertEquals($transaction, $result['transaction']);
        $this->assertEquals($callback, $result['callback']);
    }

    /**
     * 测试处理构造函数Gas估算参数（不包含交易对象）
     */
    public function testProcessConstructorGasArgumentsWithoutTransactionShouldReturnEmptyTransaction(): void
    {
        $constructorParams = ['TokenName'];
        $callback = function () {};
        $arguments = [...$constructorParams, $callback];
        $constructor = [
            'type' => 'constructor',
            'inputs' => [
                ['type' => 'string', 'name' => 'name'],
            ],
        ];
        $bytecode = '0x608060405234801561001057600080fd5b50...';

        // Mock validation calls are handled by anonymous class

        $result = $this->processor->processConstructorGasArguments($arguments, $constructor, $bytecode);

        $this->assertEmpty($result['transaction']);
        $this->assertEquals($callback, $result['callback']);
    }

    /**
     * 测试处理构造函数Gas估算参数（字节码为空时抛出异常）
     */
    public function testProcessConstructorGasArgumentsWithEmptyBytecodeShouldThrowException(): void
    {
        $callback = function () {};
        $arguments = [$callback];
        $constructor = ['inputs' => []];
        $bytecode = '';

        // Mock validation calls are handled by anonymous class

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Please call bytecode($bytecode) before estimateGas().');

        $this->processor->processConstructorGasArguments($arguments, $constructor, $bytecode);
    }

    /**
     * 测试处理构造函数Gas估算参数（多个交易参数的情况）
     */
    public function testProcessConstructorGasArgumentsWithExtraTransactionArgsShouldUseCorrectTransaction(): void
    {
        $constructorParams = ['TokenName', 'TKN'];
        $transaction = ['gas' => '2000000'];
        $callback = function () {};
        $arguments = [...$constructorParams, $transaction, $callback];
        $constructor = [
            'inputs' => [
                ['type' => 'string'],
                ['type' => 'string'],
            ],
        ];
        $bytecode = '0x123...';

        // Mock validation calls are handled by anonymous class

        // Mock validation calls are handled by anonymous class

        $result = $this->processor->processConstructorGasArguments($arguments, $constructor, $bytecode);

        $this->assertEquals($transaction, $result['transaction']);
        $this->assertEquals($callback, $result['callback']);
    }

    /**
     * 测试提取交易信息（最后参数为数组但不是交易对象）
     */
    public function testProcessFunctionArgumentsWithArrayNotTransactionShouldTreatAsParam(): void
    {
        $method = 'multiSend';
        $params = [['address1', 'address2']]; // 数组参数但不是交易对象
        $callback = function () {};
        $arguments = [$method, ...$params, $callback];

        // Mock validation calls are handled by anonymous class

        $this->setMockReturnValue(false);

        $result = $this->processor->processFunctionArguments($arguments);

        $this->assertEquals($method, $result['method']);
        $this->assertEquals($params, $result['params']);
        $this->assertEquals($callback, $result['callback']);
        $this->assertFalse($result['hasTransaction']);
        $this->assertEmpty($result['transaction']);
    }

    /**
     * 测试构造函数参数数量不足时是否正确调用验证器
     */
    public function testProcessConstructorGasArgumentsWithInsufficientArgsShouldCallValidator(): void
    {
        $callback = function () {};
        $arguments = [$callback]; // 只有回调，缺少构造函数参数
        $constructor = [
            'inputs' => [
                ['type' => 'string', 'name' => 'name'],
                ['type' => 'uint256', 'name' => 'supply'],
            ],
        ];
        $bytecode = '0x123...';

        // Mock validation calls are handled by anonymous class

        $this->setMockException(new InvalidArgumentException('Please make sure you have put all constructor params and callback.'));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Please make sure you have put all constructor params and callback.');

        $this->processor->processConstructorGasArguments($arguments, $constructor, $bytecode);
    }
}
