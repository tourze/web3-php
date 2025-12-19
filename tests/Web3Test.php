<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Eth;
use Tourze\Web3PHP\Exception\InvalidArgumentException;
use Tourze\Web3PHP\Net;
use Tourze\Web3PHP\Personal;
use Tourze\Web3PHP\Providers\HttpProvider;
use Tourze\Web3PHP\RequestManagers\HttpRequestManager;
use Tourze\Web3PHP\Shh;
use Tourze\Web3PHP\Utils;
use Tourze\Web3PHP\Web3;

/**
 * @internal
 */
#[CoversClass(Web3::class)]
final class Web3Test extends TestCase
{
    private Web3 $web3;

    protected function setUp(): void
    {
        parent::setUp();

        $this->web3 = new Web3('http://localhost:8545');
    }

    public function testConstructor(): void
    {
        $this->assertInstanceOf(Web3::class, $this->web3);
    }

    public function testGetProvider(): void
    {
        $provider = $this->web3->getProvider();
        $this->assertInstanceOf(HttpProvider::class, $provider);
    }

    public function testSetProvider(): void
    {
        $requestManager = new HttpRequestManager('http://localhost:8546', 10);
        $newProvider = new HttpProvider($requestManager);
        $this->web3->setProvider($newProvider);

        $this->assertSame($newProvider, $this->web3->getProvider());
    }

    public function testGetEth(): void
    {
        $eth = $this->web3->getEth();
        $this->assertInstanceOf(Eth::class, $eth);
    }

    public function testGetNet(): void
    {
        $net = $this->web3->getNet();
        $this->assertInstanceOf(Net::class, $net);
    }

    public function testGetPersonal(): void
    {
        $personal = $this->web3->getPersonal();
        $this->assertInstanceOf(Personal::class, $personal);
    }

    public function testGetShh(): void
    {
        $shh = $this->web3->getShh();
        $this->assertInstanceOf(Shh::class, $shh);
    }

    public function testBatch(): void
    {
        $provider = $this->web3->getProvider();
        $this->web3->batch(true);
        $this->assertTrue($provider->getIsBatch());

        $this->web3->batch(false);
        $this->assertFalse($provider->getIsBatch());
    }

    public function testWeb3CreateWithProvider(): void
    {
        $requestManager = new HttpRequestManager('http://localhost:8547', 10);
        $provider = new HttpProvider($requestManager);
        $web3 = new Web3($provider);

        $this->assertSame($provider, $web3->getProvider());
    }

    public function testInvalidProviderThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Web3(123);
    }

    public function testUtils(): void
    {
        $utils = $this->web3->getUtils();
        $this->assertInstanceOf(Utils::class, $utils);
    }

    public function testMagicGetEth(): void
    {
        $eth = $this->web3->eth;
        $this->assertInstanceOf(Eth::class, $eth);
    }

    public function testMagicGetNet(): void
    {
        $net = $this->web3->net;
        $this->assertInstanceOf(Net::class, $net);
    }

    public function testMagicGetPersonal(): void
    {
        $personal = $this->web3->personal;
        $this->assertInstanceOf(Personal::class, $personal);
    }

    public function testMagicGetShh(): void
    {
        $shh = $this->web3->shh;
        $this->assertInstanceOf(Shh::class, $shh);
    }

    public function testMagicGetUtils(): void
    {
        $utils = $this->web3->utils;
        $this->assertInstanceOf(Utils::class, $utils);
    }
}
