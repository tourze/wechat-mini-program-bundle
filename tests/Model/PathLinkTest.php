<?php

namespace WechatMiniProgramBundle\Tests\Model;

use PHPUnit\Framework\TestCase;
use Psr\Link\LinkInterface;
use WechatMiniProgramBundle\Model\PathLink;

class PathLinkTest extends TestCase
{
    public function testGetHref_returnsPath(): void
    {
        $pathLink = new PathLink('pages/index/index', []);

        $this->assertEquals('pages/index/index', $pathLink->getHref());
    }

    public function testIsTemplated_returnsFalse(): void
    {
        $pathLink = new PathLink('pages/index/index', []);

        $this->assertFalse($pathLink->isTemplated());
    }

    public function testGetRels_returnsEmptyArray(): void
    {
        $pathLink = new PathLink('pages/index/index', []);

        $this->assertEquals([], $pathLink->getRels());
    }

    public function testGetAttributes_returnsQuery(): void
    {
        $query = ['id' => '123', 'name' => 'test'];
        $pathLink = new PathLink('pages/index/index', $query);

        $this->assertEquals($query, $pathLink->getAttributes());
    }

    public function testImplementsLinkInterface(): void
    {
        $pathLink = new PathLink('pages/index/index', []);

        $this->assertInstanceOf(LinkInterface::class, $pathLink);
    }

    public function testWithComplexQueryParams(): void
    {
        $query = [
            'id' => '123',
            'filters' => [
                'status' => 'active',
                'category' => 'electronics'
            ],
            'sort' => 'price',
            'direction' => 'asc'
        ];

        $pathLink = new PathLink('pages/products/list', $query);

        $this->assertEquals('pages/products/list', $pathLink->getHref());
        $this->assertEquals($query, $pathLink->getAttributes());
    }
}
