<?php

namespace WechatMiniProgramBundle\Tests\Service;

use HttpClientBundle\Request\RequestInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use WechatMiniProgramBundle\Exception\WechatApiException;
use WechatMiniProgramBundle\Service\Client;

/**
 * @internal
 */
#[CoversClass(Client::class)]
#[RunTestsInSeparateProcesses]
final class ClientTest extends AbstractIntegrationTestCase
{
    private Client $client;

    protected function onSetUp(): void
    {
        $this->client = self::getService(Client::class);
    }

    public function testGetLabelReturnsCorrectLabel(): void
    {
        $this->assertEquals('微信小程序请求客户端', $this->client->getLabel());
    }

    public function testGetBaseUrlWithValidDomains(): void
    {
        // 清理所有环境变量以确保测试开始时没有禁用的域名
        $domains = ['api.weixin.qq.com', 'api2.weixin.qq.com', 'sh.api.weixin.qq.com', 'sz.api.weixin.qq.com', 'hk.api.weixin.qq.com'];
        foreach ($domains as $domain) {
            $key = "WECHAT_MIN_PROGRAM_DISABLE_BASE_URL_{$domain}";
            unset($_ENV[$key]);
        }

        $baseUrl = $this->client->getBaseUrl();

        // 验证结果
        $this->assertStringStartsWith('https://', $baseUrl);
        $this->assertMatchesRegularExpression('#https://[a-z0-9.]+\.weixin\.qq\.com#', $baseUrl);
        $this->assertEquals('https://api.weixin.qq.com', $baseUrl);
    }

    public function testGetBaseUrlWithSomeDomainsDisabled(): void
    {
        // 禁用第一个域名，应该使用第二个
        $_ENV['WECHAT_MIN_PROGRAM_DISABLE_BASE_URL_api.weixin.qq.com'] = true;

        try {
            $baseUrl = $this->client->getBaseUrl();
            $this->assertEquals('https://api2.weixin.qq.com', $baseUrl);
        } finally {
            unset($_ENV['WECHAT_MIN_PROGRAM_DISABLE_BASE_URL_api.weixin.qq.com']);
        }
    }

    public function testGetBaseUrlWithAllDomainsDisabled(): void
    {
        // 保存原始环境变量
        $originalEnv = [];
        $domains = ['api.weixin.qq.com', 'api2.weixin.qq.com', 'sh.api.weixin.qq.com', 'sz.api.weixin.qq.com', 'hk.api.weixin.qq.com'];

        foreach ($domains as $domain) {
            $key = "WECHAT_MIN_PROGRAM_DISABLE_BASE_URL_{$domain}";
            if (isset($_ENV[$key])) {
                $originalEnv[$key] = $_ENV[$key];
            }
            $_ENV[$key] = true;
        }

        // 测试应该抛出异常
        $this->expectException(WechatApiException::class);
        $this->expectExceptionMessage('找不到能使用的微信小程序地址');

        try {
            $this->client->getBaseUrl();
        } finally {
            // 恢复原始环境变量
            foreach ($domains as $domain) {
                $key = "WECHAT_MIN_PROGRAM_DISABLE_BASE_URL_{$domain}";
                if (isset($originalEnv[$key])) {
                    $_ENV[$key] = $originalEnv[$key];
                } else {
                    unset($_ENV[$key]);
                }
            }
        }
    }

    public function testGetRequestUrlWithHttpsPath(): void
    {
        $request = new class implements RequestInterface {
            public function getRequestPath(): string
            {
                return 'https://custom.domain.com/api/test';
            }

            public function getRequestOptions(): ?array
            {
                return null;
            }

            public function getRequestMethod(): ?string
            {
                return null;
            }
        };

        $reflection = new \ReflectionClass($this->client);
        $method = $reflection->getMethod('getRequestUrl');
        $method->setAccessible(true);

        $result = $method->invoke($this->client, $request);

        $this->assertEquals('https://custom.domain.com/api/test', $result);
    }

    public function testGetRequestUrlWithRelativePath(): void
    {
        $request = new class implements RequestInterface {
            public function getRequestPath(): string
            {
                return '/cgi-bin/test';
            }

            public function getRequestOptions(): ?array
            {
                return null;
            }

            public function getRequestMethod(): ?string
            {
                return null;
            }
        };

        $reflection = new \ReflectionClass($this->client);
        $method = $reflection->getMethod('getRequestUrl');
        $method->setAccessible(true);

        $result = $method->invoke($this->client, $request);

        $this->assertIsString($result);
        $this->assertStringStartsWith('https://api.weixin.qq.com/cgi-bin/test', $result);
    }

    public function testGetRequestMethodDefaultsToPost(): void
    {
        $request = new class implements RequestInterface {
            public function getRequestPath(): string
            {
                return '';
            }

            public function getRequestOptions(): ?array
            {
                return null;
            }

            public function getRequestMethod(): ?string
            {
                return null;
            }
        };

        $reflection = new \ReflectionClass($this->client);
        $method = $reflection->getMethod('getRequestMethod');
        $method->setAccessible(true);

        $result = $method->invoke($this->client, $request);

        $this->assertEquals('POST', $result);
    }

    public function testGetRequestMethodReturnsSpecifiedMethod(): void
    {
        $request = new class implements RequestInterface {
            public function getRequestPath(): string
            {
                return '';
            }

            public function getRequestOptions(): ?array
            {
                return null;
            }

            public function getRequestMethod(): string
            {
                return 'GET';
            }
        };

        $reflection = new \ReflectionClass($this->client);
        $method = $reflection->getMethod('getRequestMethod');
        $method->setAccessible(true);

        $result = $method->invoke($this->client, $request);

        $this->assertEquals('GET', $result);
    }

    public function testGetRequestOptionsConvertsJsonToBody(): void
    {
        $request = new class implements RequestInterface {
            public function getRequestPath(): string
            {
                return '';
            }

            /**
             * @return array<string, mixed>
             */
            public function getRequestOptions(): array
            {
                return [
                    'json' => ['key' => 'value'],
                    'headers' => ['Content-Type' => 'application/json'],
                ];
            }

            public function getRequestMethod(): ?string
            {
                return null;
            }
        };

        $reflection = new \ReflectionClass($this->client);
        $method = $reflection->getMethod('getRequestOptions');
        $method->setAccessible(true);

        $result = $method->invoke($this->client, $request);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('body', $result);
        $this->assertArrayNotHasKey('json', $result);
        $this->assertEquals('{"key":"value"}', $result['body']);
        $this->assertArrayHasKey('headers', $result);
    }

    public function testRequestMethodExists(): void
    {
        // 测试request方法存在且可以调用（不实际发送HTTP请求）
        $reflection = new \ReflectionClass($this->client);
        $this->assertTrue($reflection->hasMethod('request'));
        $this->assertTrue($reflection->getMethod('request')->isPublic());
    }
}
