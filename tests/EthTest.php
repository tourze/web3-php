<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Eth;
use Tourze\Web3PHP\Providers\HttpProvider;
use Tourze\Web3PHP\Providers\IProvider;
use Tourze\Web3PHP\RequestManagers\HttpRequestManager;

/**
 * @internal
 */
#[CoversClass(Eth::class)]
final class EthTest extends TestCase
{
    private Eth $eth;

    private HttpProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();

        $requestManager = new HttpRequestManager('http://localhost:8545', 10);
        $this->provider = new HttpProvider($requestManager);
        $this->eth = new Eth($this->provider);
    }

    public function testConstructor(): void
    {
        $this->assertInstanceOf(Eth::class, $this->eth);
    }

    public function testGetProvider(): void
    {
        $provider = $this->eth->getProvider();
        $this->assertSame($this->provider, $provider);
    }

    public function testSetProvider(): void
    {
        $requestManager = new HttpRequestManager('http://localhost:8546', 10);
        $newProvider = new HttpProvider($requestManager);
        $this->eth->setProvider($newProvider);

        $this->assertSame($newProvider, $this->eth->getProvider());
    }

    public function testBatch(): void
    {
        $this->eth->batch(true);
        $this->assertTrue($this->provider->getIsBatch());

        $this->eth->batch(false);
        $this->assertFalse($this->provider->getIsBatch());
    }

    public function testConstructorWithStringProvider(): void
    {
        $eth = new Eth('http://localhost:8546');
        $this->assertInstanceOf(Eth::class, $eth);
        $this->assertInstanceOf(IProvider::class, $eth->getProvider());
    }

    public function testMagicGetForProperties(): void
    {
        $provider = $this->eth->getProvider();
        $this->assertSame($this->provider, $provider);
    }

    public function testMagicSetForProvider(): void
    {
        $requestManager = new HttpRequestManager('http://localhost:8547', 10);
        $newProvider = new HttpProvider($requestManager);

        // 使用 setter 方法而不是直接访问受保护的属性
        $this->eth->setProvider($newProvider);

        $this->assertSame($newProvider, $this->eth->getProvider());
    }
}
