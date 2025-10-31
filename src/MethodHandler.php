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

use Tourze\Web3PHP\Exception\InvalidArgumentException;
use Tourze\Web3PHP\Exception\RuntimeException;
use Tourze\Web3PHP\Providers\IProvider;

/**
 * 方法处理器基类 - 提供通用的__call实现
 */
abstract class MethodHandler
{
    protected ?IProvider $provider = null;

    /**
     * @var array<string, mixed>
     */
    private array $methods = [];

    /**
     * 获取允许的方法列表
     *
     * @return array<int, string>
     */
    abstract protected function getAllowedMethods(): array;

    /**
     * 调用方法
     *
     * @param string $name
     * @param array<int, mixed>  $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        $this->validateProvider();

        if (!$this->isValidMethodName($name)) {
            return null;
        }

        $method = $this->buildMethodName($name);
        $this->validateMethodAllowed($method);

        $callbackResult = $this->extractCallback($arguments);
        $callback = $callbackResult['callback'];
        $arguments = $callbackResult['arguments'];
        $methodObject = $this->getMethodObject($method, $name, $arguments);

        return $this->executeMethod($methodObject, $arguments, $callback);
    }

    /**
     * 验证提供者
     */
    private function validateProvider(): void
    {
        if (null === $this->provider) {
            throw new RuntimeException('Please set provider first.');
        }
    }

    /**
     * 验证方法名是否有效
     */
    private function isValidMethodName(string $name): bool
    {
        return 1 === preg_match('/^[a-zA-Z0-9]+$/', $name);
    }

    /**
     * 构建方法名
     */
    private function buildMethodName(string $name): string
    {
        $class = explode('\\', get_class());

        return strtolower($class[1]) . '_' . $name;
    }

    /**
     * 验证方法是否被允许
     */
    private function validateMethodAllowed(string $method): void
    {
        if (!in_array($method, $this->getAllowedMethods(), true)) {
            throw new RuntimeException('Unallowed rpc method: ' . $method);
        }
    }

    /**
     * 提取回调函数
     * @param array<int, mixed> $arguments
     * @return array{callback: callable|null, arguments: array<int, mixed>}
     */
    private function extractCallback(array $arguments): array
    {
        if (null !== $this->provider && $this->provider->getIsBatch()) {
            return ['callback' => null, 'arguments' => $arguments];
        }

        $callback = array_pop($arguments);
        if (!is_callable($callback)) {
            throw new InvalidArgumentException('The last param must be callback function.');
        }

        return ['callback' => $callback, 'arguments' => $arguments];
    }

    /**
     * 获取方法对象
     * @param array<int, mixed> $arguments
     */
    private function getMethodObject(string $method, string $name, array $arguments): mixed
    {
        if (!array_key_exists($method, $this->methods)) {
            $class = explode('\\', get_class());
            $methodClass = sprintf('\Tourze\Web3PHP\Methods\%s\%s', ucfirst($class[1]), ucfirst($name));
            $this->methods[$method] = new $methodClass($method, $arguments);
        }

        return $this->methods[$method];
    }

    /**
     * 执行方法
     * @param array<int, mixed> $arguments
     */
    private function executeMethod(mixed $methodObject, array $arguments, ?callable $callback): mixed
    {
        if (!$methodObject->validate($arguments)) {
            return null;
        }

        $inputs = $methodObject->transform($arguments, $methodObject->inputFormatters);
        $methodObject->arguments = $inputs;

        return null !== $this->provider ? $this->provider->send($methodObject, $callback) : null;
    }
}
