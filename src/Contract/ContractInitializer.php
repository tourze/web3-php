<?php

declare(strict_types=1);

/**
 * 此文件是 web3.php 包的一部分。
 *
 * (c) Kuan-Cheng,Lai <alk03073135@gmail.com>
 *
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */

namespace Tourze\Web3PHP\Contract;

use Tourze\Web3PHP\Contract\AbiParser;
use Tourze\Web3PHP\Contract\ArgumentProcessor;
use Tourze\Web3PHP\Contract\ArgumentValidator;
use Tourze\Web3PHP\Contract\FunctionMatcher;
use Tourze\Web3PHP\Contracts\Ethabi;
use Tourze\Web3PHP\Contracts\Types\Address;
use Tourze\Web3PHP\Contracts\Types\Boolean;
use Tourze\Web3PHP\Contracts\Types\Bytes;
use Tourze\Web3PHP\Contracts\Types\DynamicBytes;
use Tourze\Web3PHP\Contracts\Types\Integer;
use Tourze\Web3PHP\Contracts\Types\Str;
use Tourze\Web3PHP\Contracts\Types\Uinteger;
use Tourze\Web3PHP\Eth;
use Tourze\Web3PHP\Providers\HttpProvider;
use Tourze\Web3PHP\Providers\Provider;
use Tourze\Web3PHP\RequestManagers\HttpRequestManager;

/**
 * 合约初始化器
 */
class ContractInitializer
{
    /**
     * 初始化Provider
     * @param mixed $provider
     */
    public static function initializeProvider($provider): Provider
    {
        $isValidUrl = is_string($provider) && false !== filter_var($provider, FILTER_VALIDATE_URL);
        $isHttpUrl = is_string($provider) && 1 === preg_match('/^https?:\/\//', $provider);

        if ($isValidUrl && $isHttpUrl) {
            $requestManager = new HttpRequestManager($provider);

            return new HttpProvider($requestManager);
        }

        if ($provider instanceof Provider) {
            return $provider;
        }

        throw new \InvalidArgumentException('Invalid provider type');
    }

    /**
     * 初始化以太坊组件
     * @return array{eth: Eth, ethabi: Ethabi}
     */
    public static function initializeEthereumComponents(Provider $provider): array
    {
        $eth = new Eth($provider);
        $ethabi = new Ethabi([
            'address' => new Address(),
            'bool' => new Boolean(),
            'bytes' => new Bytes(),
            'dynamicBytes' => new DynamicBytes(),
            'int' => new Integer(),
            'string' => new Str(),
            'uint' => new Uinteger(),
        ]);

        return ['eth' => $eth, 'ethabi' => $ethabi];
    }

    /**
     * 初始化服务
     * @return array{abiParser: AbiParser, argumentValidator: ArgumentValidator, argumentProcessor: ArgumentProcessor, functionMatcher: FunctionMatcher}
     */
    public static function initializeServices(Ethabi $ethabi): array
    {
        $abiParser = new AbiParser();
        $argumentValidator = new ArgumentValidator();
        $argumentProcessor = new ArgumentProcessor($argumentValidator);
        $functionMatcher = new FunctionMatcher($ethabi);

        return [
            'abiParser' => $abiParser,
            'argumentValidator' => $argumentValidator,
            'argumentProcessor' => $argumentProcessor,
            'functionMatcher' => $functionMatcher,
        ];
    }

    /**
     * 解析和设置ABI
     * @param string|\stdClass|array<int|string, mixed> $abi
     * @return array{abi: array<string, mixed>, functions: array<string, mixed>, constructor: array<string, mixed>, events: array<string, mixed>}
     */
    public static function parseAndSetAbi($abi, AbiParser $abiParser): array
    {
        $parsedAbi = $abiParser->parse($abi);

        return [
            'abi' => $parsedAbi['abi'],
            'functions' => $parsedAbi['functions'],
            'constructor' => $parsedAbi['constructor'],
            'events' => $parsedAbi['events'],
        ];
    }
}
