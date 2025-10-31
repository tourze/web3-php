<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Net;
use Tourze\Web3PHP\Providers\IProvider;

/**
 * Net 测试
 * @internal
 */
#[CoversClass(Net::class)]
final class NetTest extends TestCase
{
    /**
     * 测试构造函数
     */
    public function testConstruct(): void
    {
        $net = new Net('http://localhost:8545');

        $this->assertInstanceOf(Net::class, $net);
    }

    /**
     * 测试允许的方法列表
     */
    public function testAllowedMethods(): void
    {
        $net = new Net('http://localhost:8545');

        $reflection = new \ReflectionClass($net);
        $allowedMethods = $reflection->getProperty('allowedMethods');
        $allowedMethods->setAccessible(true);

        $expectedMethods = ['net_version', 'net_peerCount', 'net_listening'];
        $this->assertSame($expectedMethods, $allowedMethods->getValue($net));
    }

    /**
     * 测试方法属性初始化
     */
    public function testMethodsInitialization(): void
    {
        $net = new Net('http://localhost:8545');

        // 从基类MethodHandler获取methods属性
        $reflection = new \ReflectionClass('Tourze\Web3PHP\MethodHandler');
        $methods = $reflection->getProperty('methods');
        $methods->setAccessible(true);

        $this->assertIsArray($methods->getValue($net));
        $this->assertEmpty($methods->getValue($net));
    }

    /**
     * 测试provider属性
     */
    public function testProviderProperty(): void
    {
        $net = new Net('http://localhost:8545');

        $reflection = new \ReflectionClass($net);
        $provider = $reflection->getProperty('provider');
        $provider->setAccessible(true);

        $this->assertInstanceOf(IProvider::class, $provider->getValue($net));
    }

    /**
     * 测试类命名空间
     */
    public function testNamespace(): void
    {
        $reflection = new \ReflectionClass(Net::class);
        $this->assertSame('Tourze\Web3PHP', $reflection->getNamespaceName());
    }

    /**
     * 测试类属性可见性
     */
    public function testPropertiesVisibility(): void
    {
        $reflection = new \ReflectionClass(Net::class);

        // provider属性继承自基类，应该是protected
        $baseReflection = new \ReflectionClass('Tourze\Web3PHP\MethodHandler');
        $provider = $baseReflection->getProperty('provider');
        $this->assertTrue($provider->isProtected());

        // methods属性在基类中是private
        $methods = $baseReflection->getProperty('methods');
        $this->assertTrue($methods->isPrivate());

        // allowedMethods属性应该是private
        $allowedMethods = $reflection->getProperty('allowedMethods');
        $this->assertTrue($allowedMethods->isPrivate());
    }

    /**
     * 测试构造函数参数类型
     */
    public function testConstructorParameterType(): void
    {
        $reflection = new \ReflectionMethod(Net::class, '__construct');
        $parameters = $reflection->getParameters();

        $this->assertCount(1, $parameters);
        $this->assertSame('provider', $parameters[0]->getName());

        // 检查参数类型提示（可能为null，因为构造函数参数没有类型提示）
        $type = $parameters[0]->getType();
        // 由于构造函数参数没有类型提示，这里我们只验证参数存在
        $this->assertSame('provider', $parameters[0]->getName());
    }

    /**
     * 测试 batch 方法
     */
    public function testBatch(): void
    {
        $net = new Net('http://localhost:8545');

        // 获取 provider 对象
        $reflection = new \ReflectionClass($net);
        $provider = $reflection->getProperty('provider');
        $provider->setAccessible(true);
        $providerObject = $provider->getValue($net);

        // 测试设置 batch 模式
        $this->assertFalse($providerObject->isBatch());
        $net->batch(true);
        $this->assertTrue($providerObject->isBatch());
        $net->batch(false);
        $this->assertFalse($providerObject->isBatch());
    }
}
