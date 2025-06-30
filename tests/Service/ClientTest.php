<?php

namespace WechatMiniProgramBundle\Tests\Service;

use Carbon\CarbonImmutable;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use WechatMiniProgramBundle\Entity\Account;
use WechatMiniProgramBundle\Request\StableTokenRequest;
use WechatMiniProgramBundle\Service\Client;

class ClientTest extends TestCase
{
    private MockObject|CacheInterface $cache;
    private Client $client;
    private MockObject|LoggerInterface $logger;

    protected function setUp(): void
    {
        $this->cache = $this->createMock(CacheInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->client = new Client($this->cache);

        // 设置私有属性apiClientLogger
        $reflection = new \ReflectionClass(Client::class);
        $property = $reflection->getProperty('apiClientLogger');
        $property->setAccessible(true);
        $property->setValue($this->client, $this->logger);
    }

    public function testGetLabel_returnsCorrectLabel(): void
    {
        $this->assertEquals('微信小程序请求客户端', $this->client->getLabel());
    }



    public function testGetBaseUrl_withValidDomains(): void
    {
        // 执行测试
        $baseUrl = $this->client->getBaseUrl();

        // 验证结果
        $this->assertStringStartsWith('https://', $baseUrl);
        $this->assertMatchesRegularExpression('/https:\/\/[a-z0-9.]+\.weixin\.qq\.com/', $baseUrl);
    }

    public function testGetBaseUrl_withAllDomainsDisabled(): void
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
        $this->expectException(\RuntimeException::class);
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
}
