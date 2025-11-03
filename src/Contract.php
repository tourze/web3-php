<?php

/**
 * 此文件是 web3.php 包的一部分。
 *
 * (c) Kuan-Cheng,Lai <alk03073135@gmail.com>
 *
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */

namespace Tourze\Web3PHP;

use Tourze\Web3PHP\Contract\AbiParser;
use Tourze\Web3PHP\Contract\ArgumentProcessor;
use Tourze\Web3PHP\Contract\ArgumentValidator;
use Tourze\Web3PHP\Contract\ContractInitializer;
use Tourze\Web3PHP\Contract\FunctionMatcher;
use Tourze\Web3PHP\Contracts\Ethabi;
use Tourze\Web3PHP\Contracts\Types\Address;
use Tourze\Web3PHP\Contracts\Types\Boolean;
use Tourze\Web3PHP\Contracts\Types\Bytes;
use Tourze\Web3PHP\Contracts\Types\DynamicBytes;
use Tourze\Web3PHP\Contracts\Types\Integer;
use Tourze\Web3PHP\Contracts\Types\Str;
use Tourze\Web3PHP\Contracts\Types\Uinteger;
use Tourze\Web3PHP\Exception\InvalidArgumentException;
use Tourze\Web3PHP\Exception\RuntimeException;
use Tourze\Web3PHP\Formatters\AddressFormatter;
use Tourze\Web3PHP\Providers\HttpProvider;
use Tourze\Web3PHP\Providers\Provider;
use Tourze\Web3PHP\RequestManagers\HttpRequestManager;
use Tourze\Web3PHP\Validators\AddressValidator;
use Tourze\Web3PHP\Validators\HexValidator;
use Tourze\Web3PHP\Validators\StringValidator;

class Contract
{
    private AbiParser $abiParser;

    private ArgumentValidator $argumentValidator;

    private ArgumentProcessor $argumentProcessor;

    private FunctionMatcher $functionMatcher;

    protected Provider $provider;

    /** @var array<string, mixed> */
    protected array $abi;

    /** @var array<string, mixed> */
    protected array $constructor = [];

    /** @var array<string, array<string, mixed>> */
    protected array $functions = [];

    /** @var array<string, array<string, mixed>> */
    protected array $events = [];

    protected ?string $toAddress = null;

    protected ?string $bytecode = null;

    protected Eth $eth;

    protected Ethabi $ethabi;

    /** @var string|int|null */
    protected $defaultBlock;

    /**
     * 构造函数
     *
     * @param string|Provider        $provider
     * @param string|\stdClass|array<string, mixed>|array<int, array<string, mixed>> $abi
     * @param mixed $defaultBlock
     */
    public function __construct($provider, $abi, $defaultBlock = 'latest')
    {
        $this->provider = ContractInitializer::initializeProvider($provider);
        $components = ContractInitializer::initializeEthereumComponents($this->provider);
        $this->eth = $components['eth'];
        $this->ethabi = $components['ethabi'];

        $services = ContractInitializer::initializeServices($this->ethabi);
        $this->abiParser = $services['abiParser'];
        $this->argumentValidator = $services['argumentValidator'];
        $this->argumentProcessor = $services['argumentProcessor'];
        $this->functionMatcher = $services['functionMatcher'];

        $abiData = ContractInitializer::parseAndSetAbi($abi, $this->abiParser);
        $this->abi = $abiData['abi'];
        $this->functions = $abiData['functions'];
        $this->constructor = $abiData['constructor'];
        $this->events = $abiData['events'];

        $this->setDefaultBlock($defaultBlock);
    }

    /**
     * 获取属性
     * @return mixed
     */
    public function __get(string $name)
    {
        $method = 'get' . ucfirst($name);

        if (method_exists($this, $method)) {
            return $this->{$method}();
        }

        return false;
    }

    /**
     * 设置属性
     * @param mixed $value
     */
    public function __set(string $name, $value): void
    {
        $method = 'set' . ucfirst($name);

        if (method_exists($this, $method)) {
            $this->{$method}($value);
        }
    }

    // 简化的Getter方法
    public function getProvider(): Provider
    {
        return $this->provider;
    }

    /**
     * @return string|int|null
     */
    public function getDefaultBlock()
    {
        return $this->defaultBlock;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function getFunctions(): array
    {
        return $this->functions;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function getEvents(): array
    {
        return $this->events;
    }

    public function getToAddress(): ?string
    {
        return $this->toAddress;
    }

    /**
     * @return array<string, mixed>
     */
    public function getConstructor(): array
    {
        return $this->constructor;
    }

    /**
     * @return array<string, mixed>
     */
    public function getAbi(): array
    {
        return $this->abi;
    }

    public function getEthabi(): Ethabi
    {
        return $this->ethabi;
    }

    public function getEth(): Eth
    {
        return $this->eth;
    }

    /**
     * @param string|Provider $provider
     */
    public function setProvider($provider): void
    {
        if ($provider instanceof Provider) {
            $this->provider = $provider;
        }
    }

    /**
     * @param string|int|null $defaultBlock
     */
    public function setDefaultBlock($defaultBlock): void
    {
        $this->defaultBlock = $this->argumentValidator->validateDefaultBlock($defaultBlock);
    }

    /**
     * @param string|\stdClass|array<string, mixed>|array<int, array<string, mixed>> $abi
     */
    public function setAbi($abi): void
    {
        $abiData = ContractInitializer::parseAndSetAbi($abi, $this->abiParser);
        $this->abi = $abiData['abi'];
        $this->functions = $abiData['functions'];
        $this->constructor = $abiData['constructor'];
        $this->events = $abiData['events'];
    }

    /**
     * @param string $bytecode
     */
    public function setBytecode($bytecode): void
    {
        $this->bytecode($bytecode);
    }

    /**
     * @param string $address
     */
    public function setToAddress($address): void
    {
        $this->at($address);
    }

    /**
     * 设置合约地址
     */
    public function at(string $address): void
    {
        if (!AddressValidator::validate($address)) {
            throw new InvalidArgumentException('Please make sure address is valid.');
        }
        $this->toAddress = AddressFormatter::format($address);
    }

    public function getBytecode(): ?string
    {
        return null !== $this->bytecode && '' !== $this->bytecode ? '0x' . $this->bytecode : null;
    }

    /**
     * 合约字节码
     */
    public function bytecode(string $bytecode): void
    {
        if (!HexValidator::validate($bytecode)) {
            throw new InvalidArgumentException('Please make sure bytecode is valid.');
        }
        $this->bytecode = Utils::stripZero($bytecode);
    }

    /**
     * 设置ABI
     */
    public function abi(string $abi): void
    {
        if (!StringValidator::validate($abi)) {
            throw new InvalidArgumentException('Please make sure abi is valid.');
        }

        $parsedAbi = $this->abiParser->parse($abi);
        $this->abi = $parsedAbi['abi'];
        $this->functions = $parsedAbi['functions'];
        $this->constructor = $parsedAbi['constructor'];
        $this->events = $parsedAbi['events'];
    }

    /**
     * 部署新合约
     */
    public function new(): void
    {
        if ([] === $this->constructor) {
            return;
        }

        $arguments = func_get_args();

        if (null === $this->bytecode || '' === $this->bytecode) {
            throw new InvalidArgumentException('Please call bytecode($bytecode) before new().');
        }

        $constructorInputs = $this->constructor['inputs'] ?? [];
        if (!is_array($constructorInputs)) {
            throw new InvalidArgumentException('Constructor inputs must be an array.');
        }

        $inputCount = count($constructorInputs);
        if (count($arguments) < $inputCount + 1) {
            throw new InvalidArgumentException('Please make sure you have put all constructor params and callback.');
        }

        $callback = array_pop($arguments);
        $this->argumentValidator->validateCallback($callback);

        $params = array_splice($arguments, 0, $inputCount);
        $data = $this->ethabi->encodeParameters($this->constructor, $params);

        $transaction = $arguments[0] ?? [];
        if (!is_array($transaction)) {
            throw new InvalidArgumentException('Transaction data must be an array.');
        }
        $transaction['data'] = '0x' . $this->bytecode . Utils::stripZero($data);

        /** @var array<string, mixed> $transaction */
        $this->eth->sendTransaction($transaction, function ($err, $transaction) use ($callback) {
            return call_user_func($callback, $err, $err ? null : $transaction);
        });
    }

    /**
     * 发送交易
     */
    public function send(): void
    {
        if ([] === $this->functions) {
            return;
        }

        $request = $this->argumentProcessor->processFunctionArguments(func_get_args());
        $functionData = $this->functionMatcher->findMatchingFunction(
            $this->functions,
            $request['method'],
            $request['params']
        );

        $transaction = $request['transaction'];
        $transaction['to'] = $this->toAddress;
        $transaction['data'] = $this->ethabi->encodeFunctionSignature($functionData['functionName']) .
            Utils::stripZero($functionData['encodedData']);

        $this->eth->sendTransaction($transaction, function ($err, $transaction) use ($request) {
            return call_user_func($request['callback'], $err, $err ? null : $transaction);
        });
    }

    /**
     * 调用函数
     */
    public function call(): void
    {
        if ([] === $this->functions) {
            return;
        }

        $arguments = func_get_args();
        $callInfo = $this->argumentProcessor->processCallArguments($arguments, $this->defaultBlock);
        $functionMatch = $this->functionMatcher->findMatchingCallFunction($this->functions, $callInfo['method'], $arguments);

        /** @var array<int, mixed> $params */
        $params = $functionMatch['params'];
        $encodedData = $this->ethabi->encodeParameters($functionMatch['function'], $params);
        $functionName = Utils::jsonMethodToString($functionMatch['function']);

        $transaction = $callInfo['transaction'];
        $transaction['to'] = $this->toAddress;
        $transaction['data'] = $this->ethabi->encodeFunctionSignature($functionName) . Utils::stripZero($encodedData);

        $this->eth->call($transaction, $callInfo['defaultBlock'], function ($err, $result) use ($callInfo, $functionMatch) {
            if ($err) {
                return call_user_func($callInfo['callback'], $err, null);
            }

            $decodedResult = $this->ethabi->decodeParameters($functionMatch['function'], $result);

            return call_user_func($callInfo['callback'], null, $decodedResult);
        });
    }

    /**
     * 估算Gas费用
     */
    public function estimateGas(): void
    {
        if ([] === $this->functions && [] === $this->constructor) {
            return;
        }

        $arguments = func_get_args();

        if (null === $this->toAddress && null !== $this->bytecode && '' !== $this->bytecode) {
            $request = $this->argumentProcessor->processConstructorGasArguments($arguments, $this->constructor, $this->bytecode);
            $constructorInputs = $this->constructor['inputs'] ?? [];
            if (!is_array($constructorInputs)) {
                throw new InvalidArgumentException('Constructor inputs must be an array.');
            }
            $params = array_splice($arguments, 0, count($constructorInputs));
            $data = $this->ethabi->encodeParameters($this->constructor, $params);

            $transaction = $request['transaction'];
            $transaction['data'] = '0x' . $this->bytecode . Utils::stripZero($data);
            $callback = $request['callback'];
        } else {
            $request = $this->argumentProcessor->processFunctionArguments($arguments);
            $functionData = $this->functionMatcher->findMatchingFunction(
                $this->functions,
                $request['method'],
                $request['params']
            );

            $transaction = $request['transaction'];
            $transaction['to'] = $this->toAddress;
            $transaction['data'] = $this->ethabi->encodeFunctionSignature($functionData['functionName']) .
                Utils::stripZero($functionData['encodedData']);
            $callback = $request['callback'];
        }

        $this->eth->estimateGas($transaction, function ($err, $gas) use ($callback) {
            return call_user_func($callback, $err, $err ? null : $gas);
        });
    }

    /**
     * 获取数据
     */
    public function getData(): string
    {
        if ([] === $this->functions && [] === $this->constructor) {
            return '';
        }

        $arguments = func_get_args();

        if (null === $this->toAddress && null !== $this->bytecode && '' !== $this->bytecode) {
            return $this->getConstructorData($arguments);
        }

        return $this->getFunctionData($arguments);
    }

    /**
     * 获取构造函数数据
     * @param array<mixed> $arguments
     */
    private function getConstructorData(array $arguments): string
    {
        if (null === $this->bytecode || '' === $this->bytecode) {
            throw new InvalidArgumentException('Please call bytecode($bytecode) before getData().');
        }

        $constructorInputs = $this->constructor['inputs'] ?? [];
        if (!is_array($constructorInputs)) {
            throw new InvalidArgumentException('Constructor inputs must be an array.');
        }
        $inputCount = count($constructorInputs);
        if (count($arguments) < $inputCount) {
            throw new InvalidArgumentException('Please make sure you have put all constructor params.');
        }

        $params = array_splice($arguments, 0, $inputCount);
        /** @var array<int, mixed> $params */
        $data = $this->ethabi->encodeParameters($this->constructor, $params);

        return $this->bytecode . Utils::stripZero($data);
    }

    /**
     * 获取函数数据
     * @param array<mixed> $arguments
     */
    private function getFunctionData(array $arguments): string
    {
        $method = array_shift($arguments);

        if (!is_string($method)) {
            throw new InvalidArgumentException('Please make sure the method is string.');
        }

        $methodFunctions = array_filter($this->functions, fn ($func) => $func['name'] === $method);
        if ([] === $methodFunctions) {
            throw new InvalidArgumentException('Please make sure the method exists.');
        }

        foreach ($methodFunctions as $function) {
            $functionInputs = $function['inputs'] ?? [];
            if (!is_array($functionInputs)) {
                continue;
            }
            if (count($arguments) !== count($functionInputs)) {
                continue;
            }

            try {
                /** @var array<int, mixed> $arguments */
                $data = $this->ethabi->encodeParameters($function, $arguments);
                $functionName = Utils::jsonMethodToString($function);
                $functionSignature = $this->ethabi->encodeFunctionSignature($functionName);

                return Utils::stripZero($functionSignature) . Utils::stripZero($data);
            } catch (\InvalidArgumentException $e) {
                continue;
            }
        }

        throw new InvalidArgumentException('Please make sure you have put all function params.');
    }
}
