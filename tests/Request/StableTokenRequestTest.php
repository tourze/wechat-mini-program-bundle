<?php

namespace WechatMiniProgramBundle\Tests\Request;

use PHPUnit\Framework\TestCase;
use WechatMiniProgramBundle\Request\StableTokenRequest;

class StableTokenRequestTest extends TestCase
{
    private StableTokenRequest $request;

    protected function setUp(): void
    {
        $this->request = new StableTokenRequest();
    }

    public function testGetRequestPath(): void
    {
        $this->assertEquals('/cgi-bin/stable_token', $this->request->getRequestPath());
    }

    public function testGetRequestOptions_withDefaultValues(): void
    {
        // 设置必要的属性
        $this->request->setAppId('test_app_id');
        $this->request->setSecret('test_secret');

        $options = $this->request->getRequestOptions();

        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);

        // 验证默认值
        $this->assertEquals('client_credential', $options['json']['grant_type']);
        $this->assertEquals('test_app_id', $options['json']['appid']);
        $this->assertEquals('test_secret', $options['json']['secret']);
        $this->assertFalse($options['json']['force_refresh']);
    }

    public function testGetRequestOptions_withCustomValues(): void
    {
        // 设置自定义值
        $this->request->setGrantType('custom_grant_type');
        $this->request->setAppId('custom_app_id');
        $this->request->setSecret('custom_secret');
        $this->request->setForceRefresh(true);

        $options = $this->request->getRequestOptions();

        // 验证自定义值
        $this->assertEquals('custom_grant_type', $options['json']['grant_type']);
        $this->assertEquals('custom_app_id', $options['json']['appid']);
        $this->assertEquals('custom_secret', $options['json']['secret']);
        $this->assertTrue($options['json']['force_refresh']);
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

    public function testAppId_whenSet(): void
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

    public function testSecret_whenSet(): void
    {
        $this->request->setSecret('test_secret');
        $this->assertEquals('test_secret', $this->request->getSecret());
    }
}
