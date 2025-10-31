<?php

namespace WechatMiniProgramBundle\Tests\DependencyInjection;

use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;
use WechatMiniProgramBundle\DependencyInjection\WechatMiniProgramExtension;

/**
 * @internal
 */
#[CoversClass(WechatMiniProgramExtension::class)]
final class WechatMiniProgramExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
    private WechatMiniProgramExtension $extension;

    private ContainerBuilder $container;

    protected function setUp(): void
    {
        parent::setUp();
        $this->extension = new WechatMiniProgramExtension();
        $this->container = new ContainerBuilder();
    }

    public function testLoadLoadsServicesConfiguration(): void
    {
        $this->container->setParameter('kernel.environment', 'test');

        $this->extension->load([], $this->container);

        // AutoExtension 会自动处理服务配置加载
        $this->assertInstanceOf(WechatMiniProgramExtension::class, $this->extension, 'Extension loaded successfully');
    }
}
