<?php

namespace WechatMiniProgramBundle\Request;

use HttpClientBundle\Request\RequestInterface;

/**
 * openApi管理-使用AppSecret重置API调用次数
 *
 * @see https://developers.weixin.qq.com/miniprogram/dev/OpenApiDoc/openApi-mgnt/clearQuotaByAppSecret.html
 */
class ResetApiFrequencyRequest implements RequestInterface
{
    private string $appId;

    private string $appSecret;

    public function getRequestPath(): string
    {
        return '/cgi-bin/clear_quota/v2';
    }

    public function getRequestOptions(): ?array
    {
        $json = [
            'appid' => $this->getAppId(),
            'appsecret' => $this->getAppSecret(),
        ];

        return [
            'json' => $json,
        ];
    }

    public function getAppId(): string
    {
        return $this->appId;
    }

    public function setAppId(string $appId): void
    {
        $this->appId = $appId;
    }

    public function getAppSecret(): string
    {
        return $this->appSecret;
    }

    public function setAppSecret(string $appSecret): void
    {
        $this->appSecret = $appSecret;
    }

    public function getRequestMethod(): ?string
    {
        return 'POST';
    }
}
