<?php

namespace WechatMiniProgramBundle\Tests\Unit\Request;

use PHPUnit\Framework\TestCase;
use WechatMiniProgramBundle\Request\GetRidRequest;

class GetRidRequestTest extends TestCase
{
    private GetRidRequest $request;

    protected function setUp(): void
    {
        $this->request = new GetRidRequest();
    }

    public function testGetRequestPath(): void
    {
        $this->assertSame('/cgi-bin/openapi/rid/get', $this->request->getRequestPath());
    }

    public function testRidGetterAndSetter(): void
    {
        $rid = 'test_rid_12345';
        $this->request->setRid($rid);
        $this->assertSame($rid, $this->request->getRid());
    }

    public function testGetRequestOptions(): void
    {
        $rid = 'test_rid_12345';
        $this->request->setRid($rid);
        
        $options = $this->request->getRequestOptions();
        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertArrayHasKey('rid', $options['json']);
        $this->assertSame($rid, $options['json']['rid']);
    }
}