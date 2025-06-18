<?php

namespace WechatMiniProgramBundle\Service;

use Carbon\Carbon;
use HttpClientBundle\Client\ApiClient;
use HttpClientBundle\Exception\HttpClientException;
use HttpClientBundle\Request\RequestInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Tourze\BacktraceHelper\Backtrace;
use WechatMiniProgramBundle\Entity\Account;
use WechatMiniProgramBundle\Request\RawResponseAPI;
use WechatMiniProgramBundle\Request\StableTokenRequest;
use WechatMiniProgramBundle\Request\WithAccountRequest;

/**
 * 微信小程序请求客户端
 */
#[Autoconfigure(lazy: true, public: true)]
class Client extends ApiClient
{
    public function __construct(
        private readonly CacheInterface $cache,
    ) {
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
                $this->apiClientLogger?->warning('AccessToken过期，主动刷新一次', [
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
    public function getAccountAccessToken(Account $account, bool $refresh = false): array
    {
        $cacheKey = $this->getAccessTokenCacheKey($account);
        if ((bool) $refresh) {
            $this->cache->delete($cacheKey);
        }

        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($account, $refresh) {
            $request = new StableTokenRequest();
            $request->setAppId($account->getAppId());
            $request->setSecret($account->getAppSecret());
            $request->setForceRefresh($refresh);
            $result = $this->request($request);
            $result['start_time'] = Carbon::now()->getTimestamp();

            $item->expiresAfter($result['expires_in'] - 10);

            return $result;
        });
    }

    public function getBaseUrl(): string
    {
        foreach ($this->getDomains() as $domain) {
            if ((bool) isset($_ENV["WECHAT_MIN_PROGRAM_DISABLE_BASE_URL_{$domain}"]) && $_ENV["WECHAT_MIN_PROGRAM_DISABLE_BASE_URL_{$domain}"]) {
                continue;
            }

            return "https://{$domain}";
        }
        throw new \RuntimeException('找不到能使用的微信小程序地址');
    }

    /**
     * 从请求信息中判断出 AccessToken
     */
    protected function getRequestAccessToken(WithAccountRequest $request, bool $refresh = false): string
    {
        $token = $this->getAccountAccessToken($request->getAccount(), $refresh);
        if (!isset($token['access_token'])) {
            $this->apiClientLogger?->error("无法获取AccessToken[{$request->getAccount()->getAppId()}]", [
                'token' => $token,
                'account' => $request->getAccount(),
                'backtrace' => Backtrace::create()->toString(),
            ]);
            // 主动刷新一下
            $token = $this->getAccountAccessToken($request->getAccount(), true);
        }

        if (!$token) {
            return '';
        }

        return $token['access_token'] ?? '';
    }

    protected function getRequestUrl(RequestInterface $request): string
    {
        $path = $request->getRequestPath();
        if (!str_starts_with($path, 'https://')) {
            $path = trim($path, '/');
            $path = "{$this->getBaseUrl()}/{$path}";
        }

        if ((bool) $request instanceof WithAccountRequest) {
            $accessToken = $this->getRequestAccessToken($request);
            if ((bool) str_contains($path, '?')) {
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

    protected function getRequestOptions(RequestInterface $request): ?array
    {
        $options = $request->getRequestOptions();
        if ((bool) isset($options['json'])) {
            $options['body'] = json_encode($options['json']);
            unset($options['json']);
        }

        return $options;
    }

    protected function formatResponse(RequestInterface $request, ResponseInterface $response): mixed
    {
        if ((bool) $request instanceof RawResponseAPI) {
            return $response->getContent();
        }

        $json = json_decode($response->getContent(), true);

        $errCode = intval($json['errcode'] ?? 0);
        if (0 !== $errCode) {
            $this->apiClientLogger?->warning('微信接口返回错误', [
                'request' => $request,
                'json' => $json,
                'backtrace' => Backtrace::create()->toString(),
            ]);
            $errMsg = $json['errmsg'] ?? '微信接口出错';
            throw new HttpClientException($request, $response, $errMsg, $errCode);
        }

        return $json;
    }

    private function getAccessTokenCacheKey(Account $account): string
    {
        return "WechatMiniProgramBundle_getAccountAccessToken_{$account->getAppId()}";
    }

    /**
     * @see https://developers.weixin.qq.com/doc/offiaccount/Basic_Information/Interface_field_description.html
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
}
