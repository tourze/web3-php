<?php

/**
 * 此文件是 web3.php 包的一部分。
 *
 * (c) Kuan-Cheng,Lai <alk03073135@gmail.com>
 *
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */

namespace Tourze\Web3PHP\Methods;

use Tourze\Web3PHP\Exception\InvalidArgumentException;

abstract class EthMethod implements IMethod, IRPC
{
    /**
     * 方法
     *
     * @var string
     */
    protected $method = '';

    /**
     * 参数
     *
     * @var array<int, mixed>
     */
    protected $arguments = [];

    /**
     * 验证器
     *
     * @var array<int, mixed>
     */
    protected $validators = [];

    /**
     * 输入格式化器
     *
     * @var array<int, mixed>
     */
    protected $inputFormatters = [];

    /**
     * 输出格式化器
     *
     * @var array<int, mixed>
     */
    protected $outputFormatters = [];

    /**
     * 默认值
     *
     * @var array<int, mixed>
     */
    protected $defaultValues = [];

    /**
     * 构造函数
     *
     * @param string $method
     * @param array<int, mixed>  $arguments
     */
    public function __construct(string $method = '', array $arguments = [])
    {
        $this->method = $method;
        $this->arguments = $arguments;
    }

    /**
     * 转换
     *
     * @param array<string, mixed> $data
     * @param array<string, mixed> $rules
     *
     * @return array<string, mixed>
     */
    public function transform($data, $rules)
    {
        $this->validateTransformInput($data, $rules);

        foreach ($rules as $key => $rule) {
            if (!isset($data[$key])) {
                continue;
            }

            $data[$key] = $this->applyTransformRule($data[$key], $rule, $key);
        }

        return $data;
    }

    /**
     * 验证转换输入参数
     * @param mixed $data
     * @param mixed $rules
     */
    private function validateTransformInput($data, $rules): void
    {
        if (!is_array($data) || !is_array($rules)) {
            throw new InvalidArgumentException('Transform data and rules must be array.');
        }
    }

    /**
     * 应用转换规则
     * @param mixed $value
     * @param mixed $rule
     * @param mixed $key
     * @return mixed
     */
    private function applyTransformRule($value, $rule, $key)
    {
        if (is_string($rule)) {
            /** @var callable $callable */
            $callable = [$rule, 'transform'];

            return call_user_func($callable, $value, $key);
        }

        if (is_array($rule)) {
            return $this->transform($value, $rule);
        }

        if (is_callable($rule)) {
            return call_user_func($rule, $value, $key);
        }

        return $value;
    }

    /**
     * 字符串转换
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->method;
    }

    /**
     * 转换为载荷
     *
     * @return array<string, mixed>
     */
    public function toPayload(): array
    {
        return [
            'method' => $this->method,
            'params' => $this->arguments,
        ];
    }

    /**
     * 转换为载荷字符串
     *
     * @return string|false
     */
    public function toPayloadString(): string|false
    {
        return json_encode($this->toPayload());
    }

    /**
     * 验证参数
     *
     * @param array<int, mixed> $arguments
     *
     * @return bool
     */
    public function validateArguments(array $arguments): bool
    {
        foreach ($this->validators as $key => $validator) {
            if (!isset($arguments[$key])) {
                continue;
            }

            $this->validateSingleArgument($arguments[$key], $validator, $key);
        }

        return true;
    }

    /**
     * 验证单个参数
     * @param mixed $value
     * @param mixed $validator
     */
    private function validateSingleArgument($value, $validator, int $key): void
    {
        if (is_string($validator)) {
            $this->validateWithStringValidator($value, $validator, $key);

            return;
        }

        if (is_array($validator)) {
            $this->validateWithArrayValidator($value, $validator, $key);

            return;
        }
    }

    /**
     * 使用字符串验证器验证
     * @param mixed $value
     */
    private function validateWithStringValidator($value, string $validator, int $key): void
    {
        /** @var callable $callable */
        $callable = [$validator, 'validate'];
        if (!(bool) call_user_func($callable, $value)) {
            throw new InvalidArgumentException('Invalid argument at index ' . $key);
        }
    }

    /**
     * 使用数组验证器验证
     * @param mixed $value
     * @param array<int, mixed> $validators
     */
    private function validateWithArrayValidator($value, array $validators, int $key): void
    {
        foreach ($validators as $validator) {
            /** @var callable $callable */
            $callable = [$validator, 'validate'];
            if ((bool) call_user_func($callable, $value)) {
                return;
            }
        }

        throw new InvalidArgumentException('Invalid argument at index ' . $key);
    }

    /**
     * 格式化参数
     *
     * @param array<int, mixed> $arguments
     *
     * @return array<int, mixed>
     */
    public function formatArguments(array $arguments): array
    {
        // Apply default values
        foreach ($this->defaultValues as $key => $value) {
            if (!isset($arguments[$key])) {
                $arguments[$key] = $value;
            }
        }

        // Apply input formatters
        foreach ($this->inputFormatters as $key => $formatter) {
            if (isset($arguments[$key])) {
                /** @var callable $callable */
                $callable = [$formatter, 'format'];
                $arguments[$key] = call_user_func($callable, $arguments[$key]);
            }
        }

        return $arguments;
    }

    /**
     * 格式化输出
     * @param mixed $output
     */
    public function formatOutput($output): mixed
    {
        foreach ($this->outputFormatters as $formatter) {
            /** @var callable $callable */
            $callable = [$formatter, 'format'];
            $output = call_user_func($callable, $output);
        }

        return $output;
    }
}
