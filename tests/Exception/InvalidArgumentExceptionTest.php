<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;
use Tourze\Web3PHP\Exception\InvalidArgumentException;

/**
 * @internal
 */
#[CoversClass(InvalidArgumentException::class)]
final class InvalidArgumentExceptionTest extends AbstractExceptionTestCase
{
    public function testExceptionCanBeThrown(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('test message');

        throw new InvalidArgumentException('test message');
    }
}
