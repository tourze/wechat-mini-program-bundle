<?php

namespace WechatMiniProgramBundle\Tests\Service;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WechatMiniProgramBundle\Entity\LaunchOptionsAware;
use WechatMiniProgramBundle\Event\LaunchOptionsAware as LaunchOptionsAwareEvent;
use WechatMiniProgramBundle\Model\PathLink;
use WechatMiniProgramBundle\Service\LaunchOptionHelper;
use WechatMiniProgramBundle\Service\PathParserInterface;

class LaunchOptionHelperTest extends TestCase
{
    private LaunchOptionHelper $helper;
    private MockObject|PathParserInterface $pathParser;

    protected function setUp(): void
    {
        $this->pathParser = $this->createMock(PathParserInterface::class);
        $this->helper = new LaunchOptionHelper($this->pathParser);
    }

    public function testParseEvent_withLaunchOptionsAwareEvent(): void
    {
        // 创建模拟对象
        $event = new class() {
            use LaunchOptionsAwareEvent;

            public function __construct()
            {
                $this->setEnterOptions([
                    'path' => 'pages/index/index',
                    'query' => ['source' => 'direct'],
                ]);
                $this->setLaunchOptions([
                    'query' => ['campaign' => 'summer'],
                ]);
            }
        };

        // 设置预期
        $expectedLink = new PathLink('pages/index/index', [
            'source' => 'direct',
            'campaign' => 'summer',
        ]);

        $this->pathParser->expects($this->once())
            ->method('parsePath')
            ->with(
                $this->equalTo('pages/index/index'),
                $this->equalTo(['source' => 'direct', 'campaign' => 'summer'])
            )
            ->willReturn($expectedLink);

        // 执行测试
        $result = $this->helper->parseEvent($event);

        // 验证结果
        $this->assertSame($expectedLink, $result);
    }

    public function testParseEvent_withLaunchOptionsAwareEntity(): void
    {
        // 创建模拟对象
        $entity = new class() {
            use LaunchOptionsAware;

            public function __construct()
            {
                $this->setEnterOptions([
                    'path' => 'pages/product/detail',
                    'query' => ['id' => '123'],
                ]);
                $this->setLaunchOptions([
                    'query' => ['from' => 'search'],
                ]);
            }
        };

        // 设置预期
        $expectedLink = new PathLink('pages/product/detail', [
            'id' => '123',
            'from' => 'search',
        ]);

        $this->pathParser->expects($this->once())
            ->method('parsePath')
            ->with(
                $this->equalTo('pages/product/detail'),
                $this->equalTo(['id' => '123', 'from' => 'search'])
            )
            ->willReturn($expectedLink);

        // 执行测试
        $result = $this->helper->parseEvent($entity);

        // 验证结果
        $this->assertSame($expectedLink, $result);
    }

    public function testParseEvent_withNonSupportedObject(): void
    {
        // 创建不支持的对象
        $object = new \stdClass();

        // 设置预期
        $expectedLink = new PathLink('', []);

        $this->pathParser->expects($this->once())
            ->method('parsePath')
            ->with(
                $this->equalTo(''),
                $this->equalTo([])
            )
            ->willReturn($expectedLink);

        // 执行测试
        $result = $this->helper->parseEvent($object);

        // 验证结果
        $this->assertSame($expectedLink, $result);
    }

    public function testParseEvent_withEmptyLaunchOptions(): void
    {
        // 创建模拟对象，但不设置启动参数
        $entity = new class() {
            use LaunchOptionsAware;
        };

        // 修复可能的null引用错误
        $entity->setLaunchOptions(['query' => []]);

        // 设置预期
        $expectedLink = new PathLink('', []);

        $this->pathParser->expects($this->once())
            ->method('parsePath')
            ->with(
                $this->equalTo(''),
                $this->equalTo([])
            )
            ->willReturn($expectedLink);

        // 执行测试
        $result = $this->helper->parseEvent($entity);

        // 验证结果
        $this->assertSame($expectedLink, $result);
    }
}
