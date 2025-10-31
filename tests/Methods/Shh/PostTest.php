<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\Methods\Shh;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\Web3PHP\Methods\EthMethod;
use Tourze\Web3PHP\Methods\IMethod;
use Tourze\Web3PHP\Methods\IRPC;
use Tourze\Web3PHP\Methods\Shh\Post;

/**
 * Post 测试
 * @internal
 */
#[CoversClass(Post::class)]
final class PostTest extends TestCase
{
    /**
     * 测试构造函数
     */
    public function testConstruct(): void
    {
        $post = new Post();

        $this->assertInstanceOf(Post::class, $post);
        $this->assertSame('shh_post', (string) $post);
    }

    /**
     * 测试默认属性值
     */
    public function testDefaultProperties(): void
    {
        $post = new Post();

        $reflection = new \ReflectionClass($post);

        // 检查validators属性
        $validators = $reflection->getProperty('validators');
        $validators->setAccessible(true);
        $this->assertSame([
            'Tourze\Web3PHP\Validators\PostValidator',
        ], $validators->getValue($post));

        // 检查inputFormatters属性
        $inputFormatters = $reflection->getProperty('inputFormatters');
        $inputFormatters->setAccessible(true);
        $this->assertSame([
            'Tourze\Web3PHP\Formatters\PostFormatter',
        ], $inputFormatters->getValue($post));

        // 检查outputFormatters属性为空数组
        $outputFormatters = $reflection->getProperty('outputFormatters');
        $outputFormatters->setAccessible(true);
        $this->assertSame([], $outputFormatters->getValue($post));

        // 检查defaultValues属性为空数组
        $defaultValues = $reflection->getProperty('defaultValues');
        $defaultValues->setAccessible(true);
        $this->assertSame([], $defaultValues->getValue($post));
    }

    /**
     * 测试toPayload方法
     */
    public function testToPayload(): void
    {
        $post = new Post();

        $payload = $post->toPayload();

        $this->assertIsArray($payload);
        $this->assertArrayHasKey('method', $payload);
        $this->assertArrayHasKey('params', $payload);
        $this->assertSame('shh_post', $payload['method']);
        $this->assertSame([], $payload['params']);
    }

    /**
     * 测试toPayloadString方法
     */
    public function testToPayloadString(): void
    {
        $post = new Post();

        $payloadString = $post->toPayloadString();

        $this->assertIsString($payloadString);
        $this->assertStringContainsString('shh_post', $payloadString);

        // 验证是有效的JSON
        $decoded = json_decode($payloadString, true);
        $this->assertIsArray($decoded);
        $this->assertArrayHasKey('method', $decoded);
        $this->assertArrayHasKey('params', $decoded);
    }

    /**
     * 测试继承关系
     */
    public function testInheritance(): void
    {
        $post = new Post();

        $this->assertInstanceOf(EthMethod::class, $post);
        $this->assertInstanceOf(IMethod::class, $post);
        $this->assertInstanceOf(IRPC::class, $post);
    }
}
