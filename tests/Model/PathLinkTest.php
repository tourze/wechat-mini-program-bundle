<?php

namespace WechatMiniProgramBundle\Tests\Model;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use WechatMiniProgramBundle\Model\PathLink;

/**
 * @internal
 */
#[CoversClass(PathLink::class)]
final class PathLinkTest extends TestCase
{
    public function testGetHrefReturnsPath(): void
    {
        $pathLink = new PathLink('pages/index/index', []);

        $this->assertEquals('pages/index/index', $pathLink->getHref());
    }

    public function testIsTemplatedReturnsFalse(): void
    {
        $pathLink = new PathLink('pages/index/index', []);

        $this->assertFalse($pathLink->isTemplated());
    }

    public function testGetRelsReturnsEmptyArray(): void
    {
        $pathLink = new PathLink('pages/index/index', []);

        $this->assertEquals([], $pathLink->getRels());
    }

    public function testGetAttributesReturnsQuery(): void
    {
        $query = ['id' => '123', 'name' => 'test'];
        $pathLink = new PathLink('pages/index/index', $query);

        $this->assertEquals($query, $pathLink->getAttributes());
    }

    public function testWithComplexQueryParams(): void
    {
        $query = [
            'id' => '123',
            'filters' => [
                'status' => 'active',
                'category' => 'electronics',
            ],
            'sort' => 'price',
            'direction' => 'asc',
        ];

        $pathLink = new PathLink('pages/products/list', $query);

        $this->assertEquals('pages/products/list', $pathLink->getHref());
        $this->assertEquals($query, $pathLink->getAttributes());
    }
}
