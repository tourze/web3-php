<?php

declare(strict_types=1);

namespace Tourze\Web3PHP\Tests\RequestManagers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use Tourze\Web3PHP\Exception\InvalidArgumentException;
use Tourze\Web3PHP\RequestManagers\HttpRequestManager;

/**
 * @internal
 */
#[CoversClass(HttpRequestManager::class)]
final class HttpRequestManagerTest extends TestCase
{
    private HttpRequestManager $requestManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->requestManager = new HttpRequestManager('http://localhost:8545', 10);
    }

    /**
     * 创建回调状态容器
     */
    private function createCallback(): \stdClass
    {
        $state = new \stdClass();
        $state->called = false;
        $state->error = null;
        $state->result = null;
        $state->callback = function ($error, $result) use ($state): void {
            $state->called = true;
            $state->error = $error;
            $state->result = $result;
        };

        return $state;
    }

    /**
     * 创建模拟Stream对象
     */
    private function createMockStream(string $content): StreamInterface
    {
        return new class($content) extends Stream {
            private string $content;

            public function __construct(string $content)
            {
                $resource = fopen('php://memory', 'r+');
                if (false === $resource) {
                    throw new \RuntimeException('Failed to create memory stream');
                }
                parent::__construct($resource);
                $this->content = $content;
            }

            public function getContents(): string
            {
                return $this->content;
            }

            public function close(): void
            {
                // Implementation for close method
            }
        };
    }

    /**
     * 创建模拟Response对象
     */
    private function createMockResponse(string $responseBody): ResponseInterface
    {
        $mockStream = $this->createMockStream($responseBody);

        return new class($mockStream) extends Response {
            private StreamInterface $body;

            public function __construct(StreamInterface $body)
            {
                parent::__construct();
                $this->body = $body;
            }

            public function getBody(): StreamInterface
            {
                return $this->body;
            }
        };
    }

    /**
     * 创建模拟HTTP Client对象
     */
    private function createMockClient(ResponseInterface $response, ?string $expectedPayload = null): object
    {
        if (null !== $expectedPayload) {
            return new class($response, $expectedPayload) {
                private ResponseInterface $response;

                private string $expectedPayload;

                public function __construct(ResponseInterface $response, string $expectedPayload)
                {
                    $this->response = $response;
                    $this->expectedPayload = $expectedPayload;
                }

                /** @param array<string,mixed> $options */
                public function post(string $uri, array $options = []): ResponseInterface
                {
                    $expectedOptions = [
                        'headers' => ['content-type' => 'application/json'],
                        'body' => $this->expectedPayload,
                        'timeout' => 10.0,
                        'connect_timeout' => 10.0,
                    ];

                    if ('http://localhost:8545' !== $uri || $options !== $expectedOptions) {
                        throw new \RuntimeException('Unexpected post parameters');
                    }

                    return $this->response;
                }
            };
        }

        return new class($response) {
            private ResponseInterface $response;

            public function __construct(ResponseInterface $response)
            {
                $this->response = $response;
            }

            /** @param array<string,mixed> $options */
            public function post(string $uri, array $options = []): ResponseInterface
            {
                return $this->response;
            }
        };
    }

    /**
     * 创建抛出异常的模拟HTTP Client对象
     */
    private function createMockClientWithException(\Throwable $exception): object
    {
        return new class($exception) {
            private \Throwable $exception;

            public function __construct(\Throwable $exception)
            {
                $this->exception = $exception;
            }

            /** @param array<string,mixed> $options */
            public function post(string $uri, array $options = []): ResponseInterface
            {
                throw $this->exception;
            }
        };
    }

    /**
     * 使用反射替换HTTP Client
     */
    private function replaceMockClient(object $mockClient): void
    {
        $reflection = new \ReflectionClass($this->requestManager);
        $clientProperty = $reflection->getProperty('client');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($this->requestManager, $mockClient);
    }

    /**
     * 创建简单的RequestInterface实现
     */
    private function createMockRequest(): RequestInterface
    {
        $mockStream = $this->createMockStream('');
        $mockUri = $this->createMockUri();

        return new class($mockStream, $mockUri) implements RequestInterface {
            private StreamInterface $stream;

            private UriInterface $uri;

            public function __construct(StreamInterface $stream, UriInterface $uri)
            {
                $this->stream = $stream;
                $this->uri = $uri;
            }

            public function getProtocolVersion(): string
            {
                return '1.1';
            }

            public function withProtocolVersion($version): self
            {
                return $this;
            }

            public function getHeaders(): array
            {
                return [];
            }

            public function hasHeader($name): bool
            {
                return false;
            }

            public function getHeader($name): array
            {
                return [];
            }

            public function getHeaderLine($name): string
            {
                return '';
            }

            public function withHeader($name, $value): self
            {
                return $this;
            }

            public function withAddedHeader($name, $value): self
            {
                return $this;
            }

            public function withoutHeader($name): self
            {
                return $this;
            }

            public function getBody(): StreamInterface
            {
                return $this->stream;
            }

            public function withBody(StreamInterface $body): self
            {
                return $this;
            }

            public function getRequestTarget(): string
            {
                return '';
            }

            public function withRequestTarget($requestTarget): self
            {
                return $this;
            }

            public function getMethod(): string
            {
                return 'POST';
            }

            public function withMethod($method): self
            {
                return $this;
            }

            public function getUri(): UriInterface
            {
                return $this->uri;
            }

            public function withUri(UriInterface $uri, $preserveHost = false): self
            {
                return $this;
            }
        };
    }

    /**
     * 创建简单的UriInterface实现
     */
    private function createMockUri(): UriInterface
    {
        return new class implements UriInterface {
            public function getScheme(): string
            {
                return 'http';
            }

            public function getAuthority(): string
            {
                return '';
            }

            public function getUserInfo(): string
            {
                return '';
            }

            public function getHost(): string
            {
                return 'localhost';
            }

            public function getPort(): ?int
            {
                return null;
            }

            public function getPath(): string
            {
                return '/';
            }

            public function getQuery(): string
            {
                return '';
            }

            public function getFragment(): string
            {
                return '';
            }

            public function withScheme($scheme): self
            {
                return $this;
            }

            public function withUserInfo($user, $password = null): self
            {
                return $this;
            }

            public function withHost($host): self
            {
                return $this;
            }

            public function withPort($port): self
            {
                return $this;
            }

            public function withPath($path): self
            {
                return $this;
            }

            public function withQuery($query): self
            {
                return $this;
            }

            public function withFragment($fragment): self
            {
                return $this;
            }

            public function __toString(): string
            {
                return 'http://localhost/';
            }
        };
    }

    public function testConstructor(): void
    {
        $requestManager = new HttpRequestManager('http://localhost:8545', 5);
        $this->assertInstanceOf(HttpRequestManager::class, $requestManager);
        $this->assertSame('http://localhost:8545', $requestManager->getHost());
        $this->assertSame(5.0, $requestManager->getTimeout());
    }

    public function testConstructorWithDefaultTimeout(): void
    {
        $requestManager = new HttpRequestManager('http://localhost:8545');
        $this->assertSame(1.0, $requestManager->getTimeout());
    }

    public function testGetHost(): void
    {
        $this->assertSame('http://localhost:8545', $this->requestManager->getHost());
    }

    public function testGetTimeout(): void
    {
        $this->assertSame(10.0, $this->requestManager->getTimeout());
    }

    public function testMagicGetHost(): void
    {
        // Use reflection to access protected properties
        $reflection = new \ReflectionClass($this->requestManager);
        $hostProperty = $reflection->getProperty('host');
        $hostProperty->setAccessible(true);
        $host = $hostProperty->getValue($this->requestManager);
        $this->assertSame('http://localhost:8545', $host);
    }

    public function testMagicGetTimeout(): void
    {
        // Use reflection to access protected properties
        $reflection = new \ReflectionClass($this->requestManager);
        $timeoutProperty = $reflection->getProperty('timeout');
        $timeoutProperty->setAccessible(true);
        $timeout = $timeoutProperty->getValue($this->requestManager);
        $this->assertSame(10.0, $timeout);
    }

    public function testMagicGetReturnsFalseForNonExistentProperty(): void
    {
        // @phpstan-ignore-next-line 故意访问未定义属性测试魔术方法
        $result = $this->requestManager->nonExistentProperty;
        $this->assertFalse($result);
    }

    public function testMagicSetReturnsFalseForNonExistentMethod(): void
    {
        $originalHost = $this->requestManager->getHost();

        // Test that setting non-existent property doesn't change anything
        // @phpstan-ignore-next-line 故意设置未定义属性测试魔术方法
        $this->requestManager->nonExistentProperty = 'value';
        $this->assertSame($originalHost, $this->requestManager->getHost());
    }

    public function testSendPayloadThrowsExceptionForNonStringPayload(): void
    {
        // 现在由于类型声明，传入非字符串会直接抛出 TypeError
        $this->expectException(\TypeError::class);

        $callback = function ($error, $result): void {};
        // @phpstan-ignore-next-line 故意传入数组测试错误情况
        $this->requestManager->sendPayload(['not' => 'a string'], $callback);
    }

    public function testSendPayloadWithValidResponse(): void
    {
        $payload = '{"jsonrpc":"2.0","method":"eth_blockNumber","params":[],"id":1}';
        $responseBody = '{"jsonrpc":"2.0","id":1,"result":"0x1"}';

        $callback = $this->createCallback();
        $mockResponse = $this->createMockResponse($responseBody);
        $mockClient = $this->createMockClient($mockResponse, $payload);
        $this->replaceMockClient($mockClient);

        $this->requestManager->sendPayload($payload, $callback->callback);

        $this->assertTrue($callback->called);
        $this->assertNull($callback->error);
        $this->assertSame('0x1', $callback->result);
    }

    public function testSendPayloadWithBatchResponse(): void
    {
        $payload = '[{"jsonrpc":"2.0","method":"eth_blockNumber","params":[],"id":1}]';
        $responseBody = '[{"jsonrpc":"2.0","id":1,"result":"0x1"}]';

        $callback = $this->createCallback();
        $mockResponse = $this->createMockResponse($responseBody);
        $mockClient = $this->createMockClient($mockResponse);
        $this->replaceMockClient($mockClient);

        $this->requestManager->sendPayload($payload, $callback->callback);

        $this->assertTrue($callback->called);
        $this->assertNull($callback->error);
        $this->assertIsArray($callback->result);
        $this->assertSame(['0x1'], $callback->result);
    }

    public function testSendPayloadWithErrorResponse(): void
    {
        $payload = '{"jsonrpc":"2.0","method":"eth_blockNumber","params":[],"id":1}';
        $responseBody = '{"jsonrpc":"2.0","id":1,"error":{"code":-32601,"message":"Method not found"}}';

        $callback = $this->createCallback();
        $mockResponse = $this->createMockResponse($responseBody);
        $mockClient = $this->createMockClient($mockResponse);
        $this->replaceMockClient($mockClient);

        $this->requestManager->sendPayload($payload, $callback->callback);

        $this->assertTrue($callback->called);
        $this->assertInstanceOf(\RuntimeException::class, $callback->error);
        $this->assertNull($callback->result);
        $this->assertSame('Method not found', $callback->error->getMessage());
        $this->assertSame(-32601, $callback->error->getCode());
    }

    public function testSendPayloadWithInvalidJson(): void
    {
        $payload = '{"jsonrpc":"2.0","method":"eth_blockNumber","params":[],"id":1}';
        $responseBody = 'invalid json';

        $callback = $this->createCallback();
        $mockResponse = $this->createMockResponse($responseBody);
        $mockClient = $this->createMockClient($mockResponse);
        $this->replaceMockClient($mockClient);

        $this->requestManager->sendPayload($payload, $callback->callback);

        $this->assertTrue($callback->called);
        $this->assertInstanceOf(InvalidArgumentException::class, $callback->error);
        $this->assertNull($callback->result);
    }

    public function testSendPayloadWithRequestException(): void
    {
        $payload = '{"jsonrpc":"2.0","method":"eth_blockNumber","params":[],"id":1}';

        $callback = $this->createCallback();
        $mockRequest = $this->createMockRequest();
        $exception = new RequestException('Connection failed', $mockRequest);
        $mockClient = $this->createMockClientWithException($exception);
        $this->replaceMockClient($mockClient);

        $this->requestManager->sendPayload($payload, $callback->callback);

        $this->assertTrue($callback->called);
        $this->assertInstanceOf(RequestException::class, $callback->error);
        $this->assertNull($callback->result);
    }

    public function testSendPayloadWithUnknownError(): void
    {
        $payload = '{"jsonrpc":"2.0","method":"eth_blockNumber","params":[],"id":1}';
        $responseBody = '{"jsonrpc":"2.0","id":1}'; // No result or error

        $callback = $this->createCallback();
        $mockResponse = $this->createMockResponse($responseBody);
        $mockClient = $this->createMockClient($mockResponse);
        $this->replaceMockClient($mockClient);

        $this->requestManager->sendPayload($payload, $callback->callback);

        $this->assertTrue($callback->called);
        $this->assertInstanceOf(\RuntimeException::class, $callback->error);
        $this->assertNull($callback->result);
        $this->assertSame('Something wrong happened.', $callback->error->getMessage());
    }
}
