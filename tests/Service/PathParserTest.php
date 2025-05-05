<?php

namespace WechatMiniProgramBundle\Tests\Service;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Link\LinkInterface;
use Psr\Log\LoggerInterface;
use WechatMiniProgramBundle\Model\PathLink;
use WechatMiniProgramBundle\Service\PathParser;

class PathParserTest extends TestCase
{
    private PathParser $pathParser;
    private MockObject|LoggerInterface $logger;

    protected function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->pathParser = new PathParser($this->logger);
    }

    public function testParsePath_withValidPath(): void
    {
        // 设置预期的日志记录
        $this->logger->expects($this->once())
            ->method('debug')
            ->with(
                $this->equalTo('解析Header参数获取小程序访问信息'),
                $this->callback(function ($context) {
                    return $context['path'] === 'pages/index/index' &&
                        $context['query'] === ['id' => '123'];
                })
            );

        // 执行测试
        $result = $this->pathParser->parsePath('pages/index/index', ['id' => '123']);

        // 验证结果
        $this->assertInstanceOf(LinkInterface::class, $result);
        $this->assertInstanceOf(PathLink::class, $result);
        $this->assertEquals('pages/index/index', $result->getHref());
        $this->assertEquals(['id' => '123'], $result->getAttributes());
        $this->assertFalse($result->isTemplated());
        $this->assertEmpty($result->getRels());
    }

    public function testParsePath_withTrailingSlash(): void
    {
        // 执行测试
        $result = $this->pathParser->parsePath('/pages/index/index/', []);

        // 验证结果
        $this->assertEquals('pages/index/index', $result->getHref());
    }

    public function testParsePath_withLeadingSlash(): void
    {
        // 执行测试
        $result = $this->pathParser->parsePath('/pages/index/index', []);

        // 验证结果
        $this->assertEquals('pages/index/index', $result->getHref());
    }

    public function testParsePath_withEmptyPath(): void
    {
        // 执行测试
        $result = $this->pathParser->parsePath('', []);

        // 验证结果
        $this->assertEquals('', $result->getHref());
    }

    public function testParsePath_withComplexQuery(): void
    {
        $query = [
            'id' => '123',
            'name' => 'test',
            'filter' => ['status' => 'active', 'type' => 'product'],
        ];

        // 执行测试
        $result = $this->pathParser->parsePath('pages/product/detail', $query);

        // 验证结果
        $this->assertEquals('pages/product/detail', $result->getHref());
        $this->assertEquals($query, $result->getAttributes());
    }
}
