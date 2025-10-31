<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\RequestManagers;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Exception\BadMethodCallException;
use Tourze\Web3PHP\RequestManagers\HttpRequestManager;

/**
 * @internal
 */
#[CoversClass(HttpRequestManager::class)]
final class RequestManagerTest extends TestCase
{
    private HttpRequestManager $requestManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->requestManager = new HttpRequestManager('http://localhost:8545', 10);
    }

    public function testConstructor(): void
    {
        $requestManager = new HttpRequestManager('http://localhost:8545', 5);
        $this->assertInstanceOf(HttpRequestManager::class, $requestManager);
        $this->assertSame('http://localhost:8545', $requestManager->getHost());
        $this->assertSame(5.0, $requestManager->getTimeout());
    }

    public function testConstructorWithDefaultTimeout(): void
    {
        $requestManager = new HttpRequestManager('http://localhost:8545');
        $this->assertSame('http://localhost:8545', $requestManager->getHost());
        $this->assertSame(1.0, $requestManager->getTimeout());
    }

    public function testConstructorConvertsTimeoutToFloat(): void
    {
        $requestManager = new HttpRequestManager('http://localhost:8545', 5);
        $this->assertSame(5.0, $requestManager->getTimeout());

        $requestManager = new HttpRequestManager('http://localhost:8545', 10);
        $this->assertSame(10.0, $requestManager->getTimeout());
    }

    public function testGetHost(): void
    {
        $this->assertSame('http://localhost:8545', $this->requestManager->getHost());
    }

    public function testGetTimeout(): void
    {
        $this->assertSame(10.0, $this->requestManager->getTimeout());
    }

    public function testMagicGetHost(): void
    {
        // Use reflection to access protected properties
        $reflection = new \ReflectionClass($this->requestManager);
        $hostProperty = $reflection->getProperty('host');
        $hostProperty->setAccessible(true);
        $host = $hostProperty->getValue($this->requestManager);
        $this->assertSame('http://localhost:8545', $host);
    }

    public function testMagicGetTimeout(): void
    {
        // Use reflection to access protected properties
        $reflection = new \ReflectionClass($this->requestManager);
        $timeoutProperty = $reflection->getProperty('timeout');
        $timeoutProperty->setAccessible(true);
        $timeout = $timeoutProperty->getValue($this->requestManager);
        $this->assertSame(10.0, $timeout);
    }

    public function testMagicGetReturnsFalseForNonExistentProperty(): void
    {
        // @phpstan-ignore-next-line 故意访问未定义属性测试魔术方法
        $result = $this->requestManager->nonExistentProperty;
        $this->assertFalse($result);
    }

    public function testMagicGetReturnsFalseForNonExistentMethod(): void
    {
        // @phpstan-ignore-next-line 故意访问未定义属性测试魔术方法
        $result = $this->requestManager->invalidProperty;
        $this->assertFalse($result);
    }

    public function testMagicSetReturnsFalseForNonExistentMethod(): void
    {
        $originalHost = $this->requestManager->getHost();

        // Test that setting non-existent property doesn't change anything
        // @phpstan-ignore-next-line 故意设置未定义属性测试魔术方法
        $this->requestManager->nonExistentProperty = 'value';
        $this->assertSame($originalHost, $this->requestManager->getHost());
    }

    public function testMagicSetReturnsFalseForInvalidProperty(): void
    {
        $originalHost = $this->requestManager->getHost();

        // Test that setting invalid property doesn't change anything
        // @phpstan-ignore-next-line 故意设置未定义属性测试魔术方法
        $this->requestManager->invalidProperty = 'value';
        $this->assertSame($originalHost, $this->requestManager->getHost());
    }

    public function testDifferentHostValues(): void
    {
        $requestManager1 = new HttpRequestManager('https://mainnet.infura.io/v3/YOUR_PROJECT_ID');
        $this->assertSame('https://mainnet.infura.io/v3/YOUR_PROJECT_ID', $requestManager1->getHost());

        $requestManager2 = new HttpRequestManager('wss://mainnet.infura.io/ws/v3/YOUR_PROJECT_ID');
        $this->assertSame('wss://mainnet.infura.io/ws/v3/YOUR_PROJECT_ID', $requestManager2->getHost());
    }

    public function testDifferentTimeoutValues(): void
    {
        $requestManager1 = new HttpRequestManager('http://localhost:8545', 0);
        $this->assertSame(0.0, $requestManager1->getTimeout());

        $requestManager2 = new HttpRequestManager('http://localhost:8545', 30);
        $this->assertSame(30.0, $requestManager2->getTimeout());

        $requestManager3 = new HttpRequestManager('http://localhost:8545', 1);
        $this->assertSame(1.0, $requestManager3->getTimeout());
    }

    /**
     * 测试 sendPayload 方法
     * 这是一个集成测试，跳过以避免网络依赖
     */
    public function testSendPayload(): void
    {
        self::markTestSkipped('集成测试跳过：避免网络依赖。sendPayload 方法需要真实的以太坊节点或Mock HTTP客户端。');
    }
}
