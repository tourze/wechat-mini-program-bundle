<?php

namespace WechatMiniProgramBundle\Service;

use Carbon\CarbonImmutable;
use HttpClientBundle\Client\ApiClient;
use HttpClientBundle\Exception\GeneralHttpClientException;
use HttpClientBundle\Exception\HttpClientException;
use HttpClientBundle\Request\RequestInterface;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Lock\LockFactory;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Tourze\BacktraceHelper\Backtrace;
use Tourze\DoctrineAsyncInsertBundle\Service\AsyncInsertService;
use Tourze\WechatMiniProgramAppIDContracts\MiniProgramInterface;
use WechatMiniProgramBundle\Exception\WechatApiException;
use WechatMiniProgramBundle\Request\RawResponseAPI;
use WechatMiniProgramBundle\Request\StableTokenRequest;
use WechatMiniProgramBundle\Request\WithAccountRequest;

/**
 * 微信小程序请求客户端
 */
#[Autoconfigure(lazy: true, public: true)]
#[WithMonologChannel(channel: 'wechat_mini_program')]
class Client extends ApiClient
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
            if (str_contains($exception->getMessage(), 'access_token is invalid') && $request instanceof WithAccountRequest) {
                $this->logger->warning('AccessToken过期，主动刷新一次', [
                    'request' => $request,
                    'exception' => $exception,
                ]);
                // 下面这样可以强制刷新一次AccessToken
                $this->cache->delete($this->getAccessTokenCacheKey($request->getAccount()));

                return parent::request($request);
            }

            throw $exception;
        }
    }

    /**
     * 获取小程序的原始AccessToken
     * 这里的AccessToken，我们统一使用那个StableToken，然后其他地方可能还会继续用
     */
    /**
     * @return array<string, mixed>
     */
    public function getAccountAccessToken(MiniProgramInterface $account, bool $refresh = false): array
    {
        $cacheKey = $this->getAccessTokenCacheKey($account);
        if ($refresh) {
            $this->cache->delete($cacheKey);
        }

        $result = $this->cache->get($cacheKey, fn (ItemInterface $item) => $this->buildTokenCacheResult($account, $refresh, $item));

        // 缓存可能返回混合类型，需要验证格式
        if (!is_array($result)) {
            throw new WechatApiException('缓存返回数据格式错误');
        }

        return $result;
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

    /**
     * 从请求信息中判断出 AccessToken
     */
    protected function getRequestAccessToken(WithAccountRequest $request, bool $refresh = false): string
    {
        $token = $this->getAccountAccessToken($request->getAccount(), $refresh);
        if (!isset($token['access_token'])) {
            $this->logger->error("无法获取AccessToken[{$request->getAccount()->getAppId()}]", [
                'token' => $token,
                'account' => $request->getAccount(),
                'backtrace' => Backtrace::create()->toString(),
            ]);
            // 主动刷新一下
            $token = $this->getAccountAccessToken($request->getAccount(), true);
        }

        if ([] === $token) {
            return '';
        }

        $accessToken = $token['access_token'] ?? '';

        return is_string($accessToken) ? $accessToken : '';
    }

    protected function getRequestUrl(RequestInterface $request): string
    {
        $path = $request->getRequestPath();
        if (!str_starts_with($path, 'https://')) {
            $path = trim($path, '/');
            $path = "{$this->getBaseUrl()}/{$path}";
        }

        if ($request instanceof WithAccountRequest) {
            $accessToken = $this->getRequestAccessToken($request);
            if (str_contains($path, '?')) {
                $path = "{$path}&access_token={$accessToken}";
            } else {
                $path = "{$path}?access_token={$accessToken}";
            }
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

        // 确保所有键都是字符串类型
        if (null === $options) {
            return null;
        }

        /** @var array<string, mixed> $stringKeyedOptions */
        $stringKeyedOptions = [];
        foreach ($options as $key => $value) {
            $stringKey = is_string($key) ? $key : (string) $key;
            $stringKeyedOptions[$stringKey] = $value;
        }

        return $stringKeyedOptions;
    }

    protected function formatResponse(RequestInterface $request, ResponseInterface $response): mixed
    {
        if ($request instanceof RawResponseAPI) {
            return $response->getContent();
        }

        $json = json_decode($response->getContent(), true);

        if (!is_array($json)) {
            throw new GeneralHttpClientException($request, $response, '微信接口返回格式错误', 0);
        }

        $errCode = $this->getIntFromMixed($json['errcode'] ?? 0);
        if (0 !== $errCode) {
            $this->logger->warning('微信接口返回错误', [
                'request' => $request,
                'json' => $json,
                'backtrace' => Backtrace::create()->toString(),
            ]);
            $errMsg = $json['errmsg'] ?? '微信接口出错';
            $errMsg = is_string($errMsg) ? $errMsg : '微信接口出错';
            throw new GeneralHttpClientException($request, $response, $errMsg, $errCode);
        }

        return $json;
    }

    private function getAccessTokenCacheKey(MiniProgramInterface $account): string
    {
        return "WechatMiniProgramBundle_getAccountAccessToken_{$account->getAppId()}";
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
            'api.weixin.qq.com',
            'api2.weixin.qq.com',
            'sh.api.weixin.qq.com',
            'sz.api.weixin.qq.com',
            'hk.api.weixin.qq.com',
        ];
    }

    /**
     * 安全地将混合类型转换为整数
     */
    private function getIntFromMixed(mixed $value): int
    {
        if (is_int($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (int) $value;
        }

        return 0;
    }

    /**
     * 构建Token缓存结果
     *
     * @return array<string, mixed>
     */
    private function buildTokenCacheResult(MiniProgramInterface $account, bool $refresh, ItemInterface $item): array
    {
        $request = new StableTokenRequest();
        $request->setAppId($account->getAppId());
        $request->setSecret($account->getAppSecret());
        $request->setForceRefresh($refresh);
        $result = $this->request($request);

        if (!is_array($result)) {
            throw new WechatApiException('获取AccessToken失败：返回数据格式错误');
        }

        $normalizedResult = $this->normalizeTokenResponse($result);
        $expiresIn = $this->calculateExpirationTime($normalizedResult);
        $item->expiresAfter($expiresIn - 10);

        return $normalizedResult;
    }

    /**
     * 标准化Token响应数据
     *
     * @param array<mixed, mixed> $result
     * @return array<string, mixed>
     */
    private function normalizeTokenResponse(array $result): array
    {
        /** @var array<string, mixed> $stringKeyedResult */
        $stringKeyedResult = [];
        foreach ($result as $key => $value) {
            $stringKey = is_string($key) ? $key : (string) $key;
            $stringKeyedResult[$stringKey] = $value;
        }

        $stringKeyedResult['start_time'] = CarbonImmutable::now()->getTimestamp();

        return $stringKeyedResult;
    }

    /**
     * 计算Token过期时间
     *
     * @param array<string, mixed> $tokenData
     */
    private function calculateExpirationTime(array $tokenData): int
    {
        $expiresIn = $tokenData['expires_in'] ?? 7200;
        if (is_int($expiresIn)) {
            return $expiresIn;
        }

        if (is_numeric($expiresIn)) {
            return (int) $expiresIn;
        }

        return 7200;
    }
}
