<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;
use Tourze\Web3PHP\Exception\Web3Exception;
use Tourze\Web3PHP\Tests\Exception\TestWeb3Exception;

/**
 * @internal
 */
#[CoversClass(Web3Exception::class)]
final class Web3ExceptionTest extends AbstractExceptionTestCase
{
    public function testExceptionCanBeThrown(): void
    {
        $this->expectException(Web3Exception::class);
        $this->expectExceptionMessage('test message');

        throw new TestWeb3Exception('test message');
    }

    public function testExceptionInheritsFromStandardException(): void
    {
        $exception = new TestWeb3Exception();

        $this->assertInstanceOf(\Exception::class, $exception);
        $this->assertInstanceOf(\Throwable::class, $exception);
    }

    public function testExceptionWithCodeAndMessage(): void
    {
        $message = 'Web3 operation failed';
        $code = 1000;

        $exception = new TestWeb3Exception($message, $code);

        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($code, $exception->getCode());
    }

    public function testExceptionWithPreviousException(): void
    {
        $previousException = new \Exception('Previous exception');
        $exception = new TestWeb3Exception('Current exception', 0, $previousException);

        $this->assertSame($previousException, $exception->getPrevious());
    }

    public function testExceptionDefaultValues(): void
    {
        $exception = new TestWeb3Exception();

        $this->assertSame('', $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
        $this->assertNull($exception->getPrevious());
    }

    public function testExceptionWithComplexMessage(): void
    {
        $message = 'Web3 JSON-RPC error: insufficient funds for gas * price + value';
        $exception = new TestWeb3Exception($message);

        $this->assertSame($message, $exception->getMessage());
    }

    public function testExceptionChaining(): void
    {
        $rootCause = new \InvalidArgumentException('Invalid argument');
        $web3Exception = new TestWeb3Exception('Web3 error', 100, $rootCause);
        $finalException = new TestWeb3Exception('Final error', 200, $web3Exception);

        $this->assertSame($web3Exception, $finalException->getPrevious());
        $this->assertSame($rootCause, $finalException->getPrevious()->getPrevious());
    }
}
