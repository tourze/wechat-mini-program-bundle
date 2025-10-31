<?php

namespace WechatMiniProgramBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use WechatMiniProgramBundle\Model\PathLink;
use WechatMiniProgramBundle\Service\PathParser;

/**
 * @internal
 */
#[CoversClass(PathParser::class)]
#[RunTestsInSeparateProcesses]
final class PathParserTest extends AbstractIntegrationTestCase
{
    private PathParser $pathParser;

    protected function onSetUp(): void
    {
        $this->pathParser = self::getService(PathParser::class);
    }

    public function testParsePathWithValidPath(): void
    {
        // 执行测试
        $result = $this->pathParser->parsePath('pages/index/index', ['id' => '123']);

        // 验证结果
        $this->assertInstanceOf(PathLink::class, $result);
        $this->assertEquals('pages/index/index', $result->getHref());
        $this->assertEquals(['id' => '123'], $result->getAttributes());
        $this->assertFalse($result->isTemplated());
        $this->assertEmpty($result->getRels());
    }

    public function testParsePathWithTrailingSlash(): void
    {
        // 执行测试
        $path = DIRECTORY_SEPARATOR . 'pages' . DIRECTORY_SEPARATOR . 'index' . DIRECTORY_SEPARATOR . 'index' . DIRECTORY_SEPARATOR;
        $result = $this->pathParser->parsePath($path, []);

        // 验证结果
        $this->assertEquals('pages/index/index', $result->getHref());
    }

    public function testParsePathWithLeadingSlash(): void
    {
        // 执行测试
        $path = DIRECTORY_SEPARATOR . 'pages' . DIRECTORY_SEPARATOR . 'index' . DIRECTORY_SEPARATOR . 'index';
        $result = $this->pathParser->parsePath($path, []);

        // 验证结果
        $this->assertEquals('pages/index/index', $result->getHref());
    }

    public function testParsePathWithEmptyPath(): void
    {
        // 执行测试
        $result = $this->pathParser->parsePath('', []);

        // 验证结果
        $this->assertEquals('', $result->getHref());
    }

    public function testParsePathWithComplexQuery(): void
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
