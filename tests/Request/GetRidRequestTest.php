<?php

namespace WechatMiniProgramBundle\Tests\Request;

use HttpClientBundle\Test\RequestTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use WechatMiniProgramBundle\Request\GetRidRequest;

/**
 * @internal
 */
#[CoversClass(GetRidRequest::class)]
final class GetRidRequestTest extends RequestTestCase
{
    private GetRidRequest $request;

    protected function setUp(): void
    {
        parent::setUp();
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
        // @phpstan-ignore-next-line
        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
        $this->assertArrayHasKey('rid', $options['json']);
        $this->assertSame($rid, $options['json']['rid']);
    }
}
