<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Contract;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Contract\ContractInitializer;
use Tourze\Web3PHP\Providers\HttpProvider;
use Tourze\Web3PHP\Providers\Provider;

/**
 * @internal
 */
#[CoversClass(ContractInitializer::class)]
final class ContractInitializerTest extends TestCase
{
    public function testInitializeProviderWithValidUrl(): void
    {
        $provider = ContractInitializer::initializeProvider('http://localhost:8545');

        self::assertInstanceOf(HttpProvider::class, $provider);
    }

    public function testInitializeProviderWithProviderInstance(): void
    {
        $mockProvider = $this->createMock(Provider::class);
        $provider = ContractInitializer::initializeProvider($mockProvider);

        self::assertSame($mockProvider, $provider);
    }

    public function testInitializeProviderWithInvalidInput(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid provider type');

        ContractInitializer::initializeProvider('invalid');
    }

    public function testInitializeEthereumComponents(): void
    {
        $mockProvider = $this->createMock(Provider::class);
        $components = ContractInitializer::initializeEthereumComponents($mockProvider);

        self::assertArrayHasKey('eth', $components);
        self::assertArrayHasKey('ethabi', $components);
    }

    public function testInitializeServices(): void
    {
        $mockProvider = $this->createMock(Provider::class);
        $components = ContractInitializer::initializeEthereumComponents($mockProvider);
        $services = ContractInitializer::initializeServices($components['ethabi']);

        self::assertArrayHasKey('abiParser', $services);
        self::assertArrayHasKey('argumentValidator', $services);
        self::assertArrayHasKey('argumentProcessor', $services);
        self::assertArrayHasKey('functionMatcher', $services);
    }

    public function testParseAndSetAbi(): void
    {
        $mockProvider = $this->createMock(Provider::class);
        $components = ContractInitializer::initializeEthereumComponents($mockProvider);
        $services = ContractInitializer::initializeServices($components['ethabi']);

        $abi = '[]';
        $result = ContractInitializer::parseAndSetAbi($abi, $services['abiParser']);

        self::assertArrayHasKey('abi', $result);
        self::assertArrayHasKey('functions', $result);
        self::assertArrayHasKey('constructor', $result);
        self::assertArrayHasKey('events', $result);
    }
}
