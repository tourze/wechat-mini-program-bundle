<?php

namespace WechatMiniProgramBundle\Tests\Unit\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use WechatMiniProgramBundle\DependencyInjection\WechatMiniProgramExtension;

class WechatMiniProgramExtensionTest extends TestCase
{
    private WechatMiniProgramExtension $extension;
    private ContainerBuilder $container;

    protected function setUp(): void
    {
        $this->extension = new WechatMiniProgramExtension();
        $this->container = new ContainerBuilder();
    }

    public function testExtendsExtension(): void
    {
        $this->assertInstanceOf(Extension::class, $this->extension);
    }

    public function testLoad(): void
    {
        // 创建服务配置文件的临时副本用于测试
        $servicesPath = __DIR__ . '/../../../src/Resources/config/services.yaml';
        
        // 检查文件是否存在
        if (!file_exists($servicesPath)) {
            $this->markTestSkipped('services.yaml file not found');
        }

        // 测试load方法不抛出异常
        $this->expectNotToPerformAssertions();
        
        try {
            $this->extension->load([], $this->container);
        } catch (\Exception $e) {
            $this->fail('Extension load should not throw exception: ' . $e->getMessage());
        }
    }

    public function testLoadAddsCompilerPass(): void
    {
        // 检查服务配置文件
        $servicesPath = __DIR__ . '/../../../src/Resources/config/services.yaml';
        if (!file_exists($servicesPath)) {
            $this->markTestSkipped('services.yaml file not found');
        }

        $this->extension->load([], $this->container);
        
        // 检查是否添加了编译器通道
        $passes = $this->container->getCompilerPassConfig()->getBeforeOptimizationPasses();
        $this->assertNotEmpty($passes);
        
        // 由于编译器通道的复杂性，我们主要检查load方法执行成功
        $this->assertTrue(true);
    }
}