<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;
use Tourze\Web3PHP\Exception\BadMethodCallException;
use Tourze\Web3PHP\Exception\Web3Exception;

/**
 * @internal
 */
#[CoversClass(BadMethodCallException::class)]
final class BadMethodCallExceptionTest extends AbstractExceptionTestCase
{
    public function testExceptionCanBeThrown(): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('test message');

        throw new BadMethodCallException('test message');
    }

    public function testExceptionInheritsFromWeb3Exception(): void
    {
        $exception = new BadMethodCallException();

        $this->assertInstanceOf(Web3Exception::class, $exception);
        $this->assertInstanceOf(\Exception::class, $exception);
    }

    public function testExceptionWithCodeAndMessage(): void
    {
        $message = 'Invalid method call';
        $code = 123;

        $exception = new BadMethodCallException($message, $code);

        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($code, $exception->getCode());
    }

    public function testExceptionWithPreviousException(): void
    {
        $previousException = new \Exception('Previous exception');
        $exception = new BadMethodCallException('Current exception', 0, $previousException);

        $this->assertSame($previousException, $exception->getPrevious());
    }

    public function testExceptionDefaultValues(): void
    {
        $exception = new BadMethodCallException();

        $this->assertSame('', $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
        $this->assertNull($exception->getPrevious());
    }
}
