<?php

namespace WechatMiniProgramBundle\Request;

use HttpClientBundle\Request\ApiRequest;

/**
 * 获取稳定版接口调用凭据
 *
 * @see https://developers.weixin.qq.com/miniprogram/dev/OpenApiDoc/mp-access-token/getStableAccessToken.html
 */
class StableTokenRequest extends ApiRequest
{
    private string $grantType = 'client_credential';

    /**
     * @var bool 默认使用 false。1. force_refresh = false 时为普通调用模式，access_token 有效期内重复调用该接口不会更新 access_token；2. 当force_refresh = true 时为强制刷新模式，会导致上次获取的 access_token 失效，并返回新的 access_token
     */
    private bool $forceRefresh = false;

    private string $appId;

    private string $secret;

    public function getRequestPath(): string
    {
        return '/cgi-bin/stable_token';
    }

    public function getRequestOptions(): ?array
    {
        return [
            'json' => [
                'grant_type' => $this->getGrantType(),
                'appid' => $this->getAppId(),
                'secret' => $this->getSecret(),
                'force_refresh' => $this->isForceRefresh(),
            ],
        ];
    }

    public function getGrantType(): string
    {
        return $this->grantType;
    }

    public function setGrantType(string $grantType): void
    {
        $this->grantType = $grantType;
    }

    public function isForceRefresh(): bool
    {
        return $this->forceRefresh;
    }

    public function setForceRefresh(bool $forceRefresh): void
    {
        $this->forceRefresh = $forceRefresh;
    }

    public function getAppId(): string
    {
        return $this->appId;
    }

    public function setAppId(string $appId): void
    {
        $this->appId = $appId;
    }

    public function getSecret(): string
    {
        return $this->secret;
    }

    public function setSecret(string $secret): void
    {
        $this->secret = $secret;
    }
}
