<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Providers\IProvider;
use Tourze\Web3PHP\Shh;

/**
 * Shh 测试
 * @internal
 */
#[CoversClass(Shh::class)]
final class ShhTest extends TestCase
{
    /**
     * 测试构造函数
     */
    public function testConstruct(): void
    {
        $shh = new Shh('http://localhost:8545');

        $this->assertInstanceOf(Shh::class, $shh);
    }

    /**
     * 测试允许的方法列表
     */
    public function testAllowedMethods(): void
    {
        $shh = new Shh('http://localhost:8545');

        $reflection = new \ReflectionClass($shh);
        $allowedMethods = $reflection->getProperty('allowedMethods');
        $allowedMethods->setAccessible(true);

        $expectedMethods = [
            'shh_version',
            'shh_newIdentity',
            'shh_hasIdentity',
            'shh_post',
            'shh_newFilter',
            'shh_uninstallFilter',
            'shh_getFilterChanges',
            'shh_getMessages',
        ];
        $this->assertSame($expectedMethods, $allowedMethods->getValue($shh));
    }

    /**
     * 测试方法属性初始化
     */
    public function testMethodsInitialization(): void
    {
        $shh = new Shh('http://localhost:8545');

        $reflection = new \ReflectionClass($shh);
        $methods = $reflection->getProperty('methods');
        $methods->setAccessible(true);

        $this->assertIsArray($methods->getValue($shh));
        $this->assertEmpty($methods->getValue($shh));
    }

    /**
     * 测试provider属性
     */
    public function testProviderProperty(): void
    {
        $shh = new Shh('http://localhost:8545');

        $reflection = new \ReflectionClass($shh);
        $provider = $reflection->getProperty('provider');
        $provider->setAccessible(true);

        $this->assertInstanceOf(IProvider::class, $provider->getValue($shh));
    }

    /**
     * 测试类命名空间
     */
    public function testNamespace(): void
    {
        $reflection = new \ReflectionClass(Shh::class);
        $this->assertSame('Tourze\Web3PHP', $reflection->getNamespaceName());
    }

    /**
     * 测试类属性可见性
     */
    public function testPropertiesVisibility(): void
    {
        $reflection = new \ReflectionClass(Shh::class);

        // provider属性应该是protected
        $provider = $reflection->getProperty('provider');
        $this->assertTrue($provider->isProtected());

        // methods属性应该是private
        $methods = $reflection->getProperty('methods');
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
        $reflection = new \ReflectionMethod(Shh::class, '__construct');
        $parameters = $reflection->getParameters();

        $this->assertCount(1, $parameters);
        $this->assertSame('provider', $parameters[0]->getName());

        // 检查参数类型提示（由于构造函数参数没有类型提示，这里应该为null）
        $type = $parameters[0]->getType();
        $this->assertNull($type);
    }

    /**
     * 测试类文档注释
     */
    public function testClassDocumentation(): void
    {
        $reflection = new \ReflectionClass(Shh::class);
        $comment = $reflection->getDocComment();

        $this->assertIsString($comment);
        $this->assertStringContainsString('web3.php', $comment);
        $this->assertStringContainsString('MIT', $comment);
    }

    /**
     * 测试 batch 方法
     */
    public function testBatch(): void
    {
        $shh = new Shh('http://localhost:8545');

        // 获取 provider 对象
        $reflection = new \ReflectionClass($shh);
        $provider = $reflection->getProperty('provider');
        $provider->setAccessible(true);
        $providerObject = $provider->getValue($shh);

        // 测试设置 batch 模式
        $this->assertFalse($providerObject->isBatch());
        $shh->batch(true);
        $this->assertTrue($providerObject->isBatch());
        $shh->batch(false);
        $this->assertFalse($providerObject->isBatch());
    }
}
