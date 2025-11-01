<?php

namespace WechatMiniProgramBundle\Tests\Request;

use HttpClientBundle\Test\RequestTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use WechatMiniProgramBundle\Request\ResetApiFrequencyRequest;

/**
 * @internal
 */
#[CoversClass(ResetApiFrequencyRequest::class)]
final class ResetApiFrequencyRequestTest extends RequestTestCase
{
    private ResetApiFrequencyRequest $request;

    protected function setUp(): void
    {
        parent::setUp();
        $this->request = new ResetApiFrequencyRequest();
    }

    public function testGetRequestPath(): void
    {
        $this->assertSame('/cgi-bin/clear_quota/v2', $this->request->getRequestPath());
    }

    public function testGetRequestMethod(): void
    {
        $this->assertSame('POST', $this->request->getRequestMethod());
    }

    public function testAppIdGetterAndSetter(): void
    {
        $appId = 'wx123456789';
        $this->request->setAppId($appId);
        $this->assertSame($appId, $this->request->getAppId());
    }

    public function testAppSecretGetterAndSetter(): void
    {
        $appSecret = 'test_app_secret_123';
        $this->request->setAppSecret($appSecret);
        $this->assertSame($appSecret, $this->request->getAppSecret());
    }

    public function testGetRequestOptions(): void
    {
        $appId = 'wx123456789';
        $appSecret = 'test_app_secret_123';

        $this->request->setAppId($appId);
        $this->request->setAppSecret($appSecret);

        $options = $this->request->getRequestOptions();
        // @phpstan-ignore-next-line
        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
        $this->assertArrayHasKey('appid', $options['json']);
        $this->assertArrayHasKey('appsecret', $options['json']);
        $this->assertSame($appId, $options['json']['appid']);
        $this->assertSame($appSecret, $options['json']['appsecret']);
    }
}
