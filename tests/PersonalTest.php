<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Personal;
use Tourze\Web3PHP\Providers\IProvider;

/**
 * Personal 测试
 * @internal
 */
#[CoversClass(Personal::class)]
final class PersonalTest extends TestCase
{
    /**
     * 测试构造函数
     */
    public function testConstruct(): void
    {
        $personal = new Personal('http://localhost:8545');

        $this->assertInstanceOf(Personal::class, $personal);
    }

    /**
     * 测试允许的方法列表
     */
    public function testAllowedMethods(): void
    {
        $personal = new Personal('http://localhost:8545');

        $reflection = new \ReflectionClass($personal);
        $allowedMethods = $reflection->getProperty('allowedMethods');
        $allowedMethods->setAccessible(true);

        $expectedMethods = [
            'personal_listAccounts',
            'personal_newAccount',
            'personal_unlockAccount',
            'personal_lockAccount',
            'personal_sendTransaction',
        ];
        $this->assertSame($expectedMethods, $allowedMethods->getValue($personal));
    }

    /**
     * 测试方法属性初始化
     */
    public function testMethodsInitialization(): void
    {
        $personal = new Personal('http://localhost:8545');

        // 从基类MethodHandler获取methods属性
        $reflection = new \ReflectionClass('Tourze\Web3PHP\MethodHandler');
        $methods = $reflection->getProperty('methods');
        $methods->setAccessible(true);

        $this->assertIsArray($methods->getValue($personal));
        $this->assertEmpty($methods->getValue($personal));
    }

    /**
     * 测试provider属性
     */
    public function testProviderProperty(): void
    {
        $personal = new Personal('http://localhost:8545');

        $reflection = new \ReflectionClass($personal);
        $provider = $reflection->getProperty('provider');
        $provider->setAccessible(true);

        $this->assertInstanceOf(IProvider::class, $provider->getValue($personal));
    }

    /**
     * 测试类命名空间
     */
    public function testNamespace(): void
    {
        $reflection = new \ReflectionClass(Personal::class);
        $this->assertSame('Tourze\Web3PHP', $reflection->getNamespaceName());
    }

    /**
     * 测试类属性可见性
     */
    public function testPropertiesVisibility(): void
    {
        $reflection = new \ReflectionClass(Personal::class);

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
        $reflection = new \ReflectionMethod(Personal::class, '__construct');
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
        $reflection = new \ReflectionClass(Personal::class);
        $comment = $reflection->getDocComment();

        $this->assertIsString($comment);
        $this->assertStringContainsString('web3.php package', $comment);
        $this->assertStringContainsString('MIT', $comment);
    }

    /**
     * 测试 batch 方法
     */
    public function testBatch(): void
    {
        $personal = new Personal('http://localhost:8545');

        // 获取 provider 对象
        $reflection = new \ReflectionClass($personal);
        $provider = $reflection->getProperty('provider');
        $provider->setAccessible(true);
        $providerObject = $provider->getValue($personal);

        // 测试设置 batch 模式
        $this->assertFalse($providerObject->isBatch());
        $personal->batch(true);
        $this->assertTrue($providerObject->isBatch());
        $personal->batch(false);
        $this->assertFalse($providerObject->isBatch());
    }
}
