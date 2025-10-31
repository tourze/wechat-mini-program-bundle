<?php

declare(strict_types=1);

namespace WechatMiniProgramBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Psr\Link\LinkInterface;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use WechatMiniProgramBundle\Entity\LaunchOptionsAware as LaunchOptionsAwareEntity;
use WechatMiniProgramBundle\Event\LaunchOptionsAware as LaunchOptionsAwareEvent;
use WechatMiniProgramBundle\Service\LaunchOptionHelper;
use WechatMiniProgramBundle\Service\PathParserInterface;

/**
 * @internal
 */
#[CoversClass(LaunchOptionHelper::class)]
#[RunTestsInSeparateProcesses]
final class LaunchOptionHelperTest extends AbstractIntegrationTestCase
{
    private LaunchOptionHelper $launchOptionHelper;

    protected function onSetUp(): void
    {
        $this->launchOptionHelper = self::getService(LaunchOptionHelper::class);
    }

    public function testParseEventWithLaunchOptionsAwareEvent(): void
    {
        $object = new class {
            use LaunchOptionsAwareEvent;

            public function __construct()
            {
                $this->setEnterOptions([
                    'path' => '/pages/index',
                    'query' => ['param1' => 'value1'],
                ]);
                $this->setLaunchOptions([
                    'query' => ['param2' => 'value2'],
                ]);
            }
        };

        $result = $this->launchOptionHelper->parseEvent($object);
        $this->assertInstanceOf(LinkInterface::class, $result);
    }

    public function testParseEventWithLaunchOptionsAwareEntity(): void
    {
        $object = new class {
            use LaunchOptionsAwareEntity;

            public function __construct()
            {
                $this->setEnterOptions([
                    'path' => '/pages/detail',
                    'query' => ['id' => '123'],
                ]);
                $this->setLaunchOptions([
                    'query' => ['source' => 'test'],
                ]);
            }
        };

        $result = $this->launchOptionHelper->parseEvent($object);
        $this->assertInstanceOf(LinkInterface::class, $result);
    }

    public function testParseEventWithoutLaunchOptionsAware(): void
    {
        $object = new class {
            // 没有使用任何 LaunchOptionsAware trait
        };

        $result = $this->launchOptionHelper->parseEvent($object);
        $this->assertInstanceOf(LinkInterface::class, $result);
    }

    public function testParseEventWithEmptyOptions(): void
    {
        $object = new class {
            use LaunchOptionsAwareEvent;

            public function __construct()
            {
                // 不设置任何选项
            }
        };

        $result = $this->launchOptionHelper->parseEvent($object);
        $this->assertInstanceOf(LinkInterface::class, $result);
    }

    public function testParseEventWithInvalidOptions(): void
    {
        $object = new class {
            use LaunchOptionsAwareEvent;

            public function __construct()
            {
                // 设置 null 选项来测试边界情况
                $this->setEnterOptions(null);
                $this->setLaunchOptions(null);
            }
        };

        $result = $this->launchOptionHelper->parseEvent($object);
        $this->assertInstanceOf(LinkInterface::class, $result);
    }

    public function testParseEventWithMergedQuery(): void
    {
        $object = new class {
            use LaunchOptionsAwareEvent;

            public function __construct()
            {
                $this->setEnterOptions([
                    'path' => '/pages/home',
                    'query' => ['enter' => 'true', 'shared' => 'enter'],
                ]);
                $this->setLaunchOptions([
                    'query' => ['launch' => 'true', 'shared' => 'launch'],
                ]);
            }
        };

        $result = $this->launchOptionHelper->parseEvent($object);
        $this->assertInstanceOf(LinkInterface::class, $result);
    }

    public function testParseEventWithNonStringKeys(): void
    {
        $object = new class {
            use LaunchOptionsAwareEvent;

            public function __construct()
            {
                $this->setEnterOptions([
                    'path' => '/pages/test',
                    'query' => ['value', 'another'], // 数字索引
                ]);
            }
        };

        $result = $this->launchOptionHelper->parseEvent($object);
        $this->assertInstanceOf(LinkInterface::class, $result);
    }
}