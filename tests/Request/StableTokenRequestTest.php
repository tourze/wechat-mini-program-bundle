<?php

namespace WechatMiniProgramBundle\Tests\Request;

use HttpClientBundle\Test\RequestTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use WechatMiniProgramBundle\Request\StableTokenRequest;

/**
 * @internal
 */
#[CoversClass(StableTokenRequest::class)]
final class StableTokenRequestTest extends RequestTestCase
{
    private StableTokenRequest $request;

    protected function setUp(): void
    {
        parent::setUp();
        $this->request = new StableTokenRequest();
    }

    public function testGetRequestPath(): void
    {
        $this->assertEquals('/cgi-bin/stable_token', $this->request->getRequestPath());
    }

    public function testGetRequestOptionsWithDefaultValues(): void
    {
        // 设置必要的属性
        $this->request->setAppId('test_app_id');
        $this->request->setSecret('test_secret');

        $options = $this->request->getRequestOptions();
        $this->assertArrayHasKey('json', $options);

        $jsonData = $options['json'];
        $this->assertIsArray($jsonData);

        // 验证默认值
        $this->assertEquals('client_credential', $jsonData['grant_type']);
        $this->assertEquals('test_app_id', $jsonData['appid']);
        $this->assertEquals('test_secret', $jsonData['secret']);
        $this->assertFalse($jsonData['force_refresh']);
    }

    public function testGetRequestOptionsWithCustomValues(): void
    {
        // 设置自定义值
        $this->request->setGrantType('custom_grant_type');
        $this->request->setAppId('custom_app_id');
        $this->request->setSecret('custom_secret');
        $this->request->setForceRefresh(true);

        $options = $this->request->getRequestOptions();
        $this->assertArrayHasKey('json', $options);

        $jsonData = $options['json'];
        $this->assertIsArray($jsonData);

        // 验证自定义值
        $this->assertEquals('custom_grant_type', $jsonData['grant_type']);
        $this->assertEquals('custom_app_id', $jsonData['appid']);
        $this->assertEquals('custom_secret', $jsonData['secret']);
        $this->assertTrue($jsonData['force_refresh']);
    }

    public function testGrantType(): void
    {
        // 默认值
        $this->assertEquals('client_credential', $this->request->getGrantType());

        // 设置并获取
        $this->request->setGrantType('custom_grant_type');
        $this->assertEquals('custom_grant_type', $this->request->getGrantType());
    }

    public function testForceRefresh(): void
    {
        // 默认值
        $this->assertFalse($this->request->isForceRefresh());

        // 设置并获取
        $this->request->setForceRefresh(true);
        $this->assertTrue($this->request->isForceRefresh());
    }

    public function testAppId(): void
    {
        // 初始状态应该抛出错误（未设置）
        $this->expectException(\Error::class);
        $this->request->getAppId();
    }

    public function testAppIdWhenSet(): void
    {
        $this->request->setAppId('test_app_id');
        $this->assertEquals('test_app_id', $this->request->getAppId());
    }

    public function testSecret(): void
    {
        // 初始状态应该抛出错误（未设置）
        $this->expectException(\Error::class);
        $this->request->getSecret();
    }

    public function testSecretWhenSet(): void
    {
        $this->request->setSecret('test_secret');
        $this->assertEquals('test_secret', $this->request->getSecret());
    }
}
