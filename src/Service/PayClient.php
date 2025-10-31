<?php

namespace WechatMiniProgramBundle\Service;

use Carbon\CarbonImmutable;
use HttpClientBundle\Client\ApiClient;
use HttpClientBundle\Exception\HttpClientException;
use HttpClientBundle\Request\RequestInterface;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Lock\LockFactory;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Tourze\DoctrineAsyncInsertBundle\Service\AsyncInsertService;
use WechatMiniProgramBundle\Exception\WechatApiException;

/**
 * 微信小程序请求客户端
 */
#[Autoconfigure(lazy: true, public: true)]
#[WithMonologChannel(channel: 'wechat_mini_program')]
class PayClient extends ApiClient
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly CacheInterface $cache,
        private readonly HttpClientInterface $httpClient,
        private readonly LockFactory $lockFactory,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly AsyncInsertService $asyncInsertService,
    ) {
    }

    protected function getLockFactory(): LockFactory
    {
        return $this->lockFactory;
    }

    protected function getHttpClient(): HttpClientInterface
    {
        return $this->httpClient;
    }

    protected function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    protected function getCache(): CacheInterface
    {
        return $this->cache;
    }

    protected function getEventDispatcher(): EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }

    protected function getAsyncInsertService(): AsyncInsertService
    {
        return $this->asyncInsertService;
    }

    public function getLabel(): string
    {
        return '微信小程序请求客户端';
    }

    public function request(RequestInterface $request): mixed
    {
        try {
            return parent::request($request);
        } catch (HttpClientException $exception) {
            throw $exception;
        }
    }

    public function getBaseUrl(): string
    {
        foreach ($this->getDomains() as $domain) {
            if (isset($_ENV["WECHAT_MIN_PROGRAM_DISABLE_BASE_URL_{$domain}"]) && '' !== $_ENV["WECHAT_MIN_PROGRAM_DISABLE_BASE_URL_{$domain}"]) {
                continue;
            }

            return "https://{$domain}";
        }
        throw new WechatApiException('找不到能使用的微信小程序地址');
    }

    protected function getRequestUrl(RequestInterface $request): string
    {
        $path = $request->getRequestPath();
        if (!str_starts_with($path, 'https://')) {
            $path = trim($path, '/');
            $path = "{$this->getBaseUrl()}/{$path}";
        }

        return $path;
    }

    protected function getRequestMethod(RequestInterface $request): string
    {
        return $request->getRequestMethod() ?? 'POST';
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function getRequestOptions(RequestInterface $request): ?array
    {
        $options = $request->getRequestOptions();
        if (isset($options['json'])) {
            $options['body'] = json_encode($options['json']);
            unset($options['json']);
        }

        // 处理SSL证书配置（用于双向认证）
        if (isset($options['local_cert'], $options['local_pk'])) {
            $options['local_cert'] = $options['local_cert'];
            $options['local_pk'] = $options['local_pk'];

            // 设置SSL验证选项
            $options['verify_peer'] = true;
            $options['verify_host'] = true;
        }

        return $options;
    }

    protected function formatResponse(RequestInterface $request, ResponseInterface $response): mixed
    {
        return $response->getContent();
    }

    /**
     * @see https://developers.weixin.qq.com/doc/offiaccount/Basic_Information/Interface_field_description.html
     */
    /**
     * @return string[]
     */
    private function getDomains(): array
    {
        return [
            'api.mch.weixin.qq.com',
        ];
    }
}
