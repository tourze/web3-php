<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;
use Tourze\Web3PHP\Exception\RuntimeException;
use Tourze\Web3PHP\Exception\Web3Exception;

/**
 * @internal
 */
#[CoversClass(RuntimeException::class)]
final class RuntimeExceptionTest extends AbstractExceptionTestCase
{
    public function testExceptionCanBeThrown(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('test message');

        throw new RuntimeException('test message');
    }

    public function testExceptionInheritsFromWeb3Exception(): void
    {
        $exception = new RuntimeException();

        $this->assertInstanceOf(Web3Exception::class, $exception);
        $this->assertInstanceOf(\Exception::class, $exception);
    }

    public function testExceptionWithCodeAndMessage(): void
    {
        $message = 'Runtime error occurred';
        $code = 500;

        $exception = new RuntimeException($message, $code);

        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($code, $exception->getCode());
    }

    public function testExceptionWithPreviousException(): void
    {
        $previousException = new \Exception('Previous exception');
        $exception = new RuntimeException('Current exception', 0, $previousException);

        $this->assertSame($previousException, $exception->getPrevious());
    }

    public function testExceptionDefaultValues(): void
    {
        $exception = new RuntimeException();

        $this->assertSame('', $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
        $this->assertNull($exception->getPrevious());
    }
}
