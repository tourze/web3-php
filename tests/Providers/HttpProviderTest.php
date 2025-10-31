<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Providers;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Exception\InvalidArgumentException;
use Tourze\Web3PHP\Exception\RuntimeException;
use Tourze\Web3PHP\Methods\Eth\BlockNumber;
use Tourze\Web3PHP\Providers\HttpProvider;
use Tourze\Web3PHP\RequestManagers\HttpRequestManager;

/**
 * @internal
 */
#[CoversClass(HttpProvider::class)]
final class HttpProviderTest extends TestCase
{
    private HttpProvider $provider;

    private HttpRequestManager $requestManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->requestManager = new HttpRequestManager('http://localhost:8545', 10);
        $this->provider = new HttpProvider($this->requestManager);
    }

    public function testConstructor(): void
    {
        $this->assertInstanceOf(HttpProvider::class, $this->provider);
        $this->assertSame($this->requestManager, $this->provider->getRequestManager());
    }

    public function testGetRequestManager(): void
    {
        $requestManager = $this->provider->getRequestManager();
        $this->assertInstanceOf(HttpRequestManager::class, $requestManager);
        $this->assertSame($this->requestManager, $requestManager);
    }

    public function testGetIsBatch(): void
    {
        $this->assertFalse($this->provider->getIsBatch());
    }

    public function testBatch(): void
    {
        $this->assertFalse($this->provider->getIsBatch());

        $this->provider->batch(true);
        $this->assertTrue($this->provider->getIsBatch());

        $this->provider->batch(false);
        $this->assertFalse($this->provider->getIsBatch());
    }

    public function testBatchWithNonBooleanValue(): void
    {
        // 在 PHP 8.0+ 中，类型不匹配会抛出 TypeError
        $this->expectException(\TypeError::class);

        // @phpstan-ignore-next-line 故意传入字符串测试错误情况
        $this->provider->batch('not_a_boolean');
    }

    public function testSendCallsCallback(): void
    {
        $method = new BlockNumber('http://localhost:8545', []);
        $callbackCalled = false;
        $callbackError = null;
        $callbackResult = null;

        $callback = function ($error, $result) use (&$callbackCalled, &$callbackError, &$callbackResult): void {
            $callbackCalled = true;
            $callbackError = $error;
            $callbackResult = $result;
        };

        // Create anonymous class to replace mock
        $mockRequestManager = new class extends HttpRequestManager {
            public function __construct()
            {
                // Call parent constructor with minimal parameters to satisfy inheritance
                parent::__construct('http://localhost:8545', 10);
            }

            public function sendPayload(string $payload, callable $callback): void
            {
                // Simulate successful response
                call_user_func($callback, null, '0x1');
            }
        };

        $provider = new HttpProvider($mockRequestManager);
        $provider->send($method, $callback);

        $this->assertTrue($callbackCalled);
        $this->assertNull($callbackError);
        $this->assertNotNull($callbackResult);
    }

    public function testSendInBatchMode(): void
    {
        $method = new BlockNumber('http://localhost:8545', []);
        $callback = function ($error, $result): void {};

        $this->provider->batch(true);
        $this->provider->send($method, $callback);

        // In batch mode, the method should be stored for later execution
        $this->assertTrue($this->provider->getIsBatch());
    }

    public function testExecuteThrowsExceptionWhenNotInBatchMode(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Please batch json rpc first.');

        $callback = function ($error, $result): void {};
        $this->provider->execute($callback);
    }

    public function testExecuteInBatchMode(): void
    {
        $method = new BlockNumber('http://localhost:8545', []);
        $callbackCalled = false;

        // Create anonymous class to replace mock
        $mockRequestManager = new class extends HttpRequestManager {
            public function __construct()
            {
                // Call parent constructor with minimal parameters to satisfy inheritance
                parent::__construct('http://localhost:8545', 10);
            }

            public function sendPayload(string $payload, callable $callback): void
            {
                // Simulate batch response
                call_user_func($callback, null, ['0x1']);
            }
        };

        $provider = new HttpProvider($mockRequestManager);
        $provider->batch(true);
        $provider->send($method, function (): void {});

        $callback = function ($error, $result) use (&$callbackCalled): void {
            $callbackCalled = true;
        };

        $provider->execute($callback);
        $this->assertTrue($callbackCalled);
    }

    public function testMagicGetMethod(): void
    {
        // Use reflection to access protected properties
        $reflection = new \ReflectionClass($this->provider);

        $isBatchProperty = $reflection->getProperty('isBatch');
        $isBatchProperty->setAccessible(true);
        $isBatch = $isBatchProperty->getValue($this->provider);
        $this->assertFalse($isBatch);

        $requestManagerProperty = $reflection->getProperty('requestManager');
        $requestManagerProperty->setAccessible(true);
        $requestManager = $requestManagerProperty->getValue($this->provider);
        $this->assertSame($this->requestManager, $requestManager);
    }

    public function testMagicGetMethodReturnsFalseForNonExistentProperty(): void
    {
        // @phpstan-ignore-next-line 故意访问未定义属性测试魔术方法
        $result = $this->provider->nonExistentProperty;
        $this->assertFalse($result);
    }

    public function testMagicSetMethod(): void
    {
        $newRequestManager = new HttpRequestManager('http://localhost:8546', 5);

        // Use reflection to access protected properties
        $reflection = new \ReflectionClass($this->provider);
        $requestManagerProperty = $reflection->getProperty('requestManager');
        $requestManagerProperty->setAccessible(true);

        // Test that non-existent setter doesn't change the property
        $originalRequestManager = $requestManagerProperty->getValue($this->provider);
        // @phpstan-ignore-next-line 故意设置未定义属性测试魔术方法
        $this->provider->nonExistentProperty = $newRequestManager;
        $this->assertSame($originalRequestManager, $requestManagerProperty->getValue($this->provider));
    }
}
