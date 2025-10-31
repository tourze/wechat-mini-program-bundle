<?php

namespace WechatMiniProgramBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminMenuTestCase;
use WechatMiniProgramBundle\Service\AdminMenu;

/**
 * @internal
 */
#[CoversClass(AdminMenu::class)]
#[RunTestsInSeparateProcesses]
final class AdminMenuTest extends AbstractEasyAdminMenuTestCase
{
    protected function onSetUp(): void
    {
        // 集成测试基类要求的抽象方法
    }

    public function testServiceIsAvailableInContainer(): void
    {
        $adminMenu = self::getService(AdminMenu::class);
        $this->assertInstanceOf(AdminMenu::class, $adminMenu);
        $this->assertInstanceOf(MenuProviderInterface::class, $adminMenu);
    }

    public function testInvokeAddsWechatMiniProgramMenu(): void
    {
        $adminMenu = self::getService(AdminMenu::class);

        // 创建 Mock 菜单项
        $miniProgramConfigItem = $this->createMock(\Knp\Menu\ItemInterface::class);
        $wechatMiniProgramItem = $this->createMock(\Knp\Menu\ItemInterface::class);
        $rootItem = $this->createMock(\Knp\Menu\ItemInterface::class);

        // 设置预期调用：第一次 getChild 返回 null，第二次返回 wechatMiniProgramItem
        $rootItem->expects($this->exactly(2))
            ->method('getChild')
            ->with('微信小程序')
            ->willReturnOnConsecutiveCalls(null, $wechatMiniProgramItem);

        // 设置 addChild 预期：应该被调用一次
        $rootItem->expects($this->once())
            ->method('addChild')
            ->with('微信小程序')
            ->willReturn($wechatMiniProgramItem);

        // 设置 wechatMiniProgramItem 的 addChild 预期
        $wechatMiniProgramItem->expects($this->once())
            ->method('addChild')
            ->with('小程序配置')
            ->willReturn($miniProgramConfigItem);

        // 设置 miniProgramConfigItem 的 setUri 预期
        $miniProgramConfigItem->expects($this->once())
            ->method('setUri')
            ->with('/admin?crudAction=index&crudControllerFqcn=WechatMiniProgramBundle%5CEntity%5CAccount')
            ->willReturnSelf();

        $adminMenu($rootItem);
    }

    public function testInvokeWorksWhenWechatMiniProgramMenuAlreadyExists(): void
    {
        $adminMenu = self::getService(AdminMenu::class);

        // 创建 Mock 菜单项
        $miniProgramConfigItem = $this->createMock(\Knp\Menu\ItemInterface::class);
        $wechatMiniProgramItem = $this->createMock(\Knp\Menu\ItemInterface::class);
        $rootItem = $this->createMock(\Knp\Menu\ItemInterface::class);

        // 设置预期调用：两次 getChild 都返回 wechatMiniProgramItem（菜单已存在）
        $rootItem->expects($this->exactly(2))
            ->method('getChild')
            ->with('微信小程序')
            ->willReturn($wechatMiniProgramItem);

        // 不应该调用 addChild（因为菜单已存在）
        $rootItem->expects($this->never())
            ->method('addChild');

        // 设置 wechatMiniProgramItem 的 addChild 预期
        $wechatMiniProgramItem->expects($this->once())
            ->method('addChild')
            ->with('小程序配置')
            ->willReturn($miniProgramConfigItem);

        // 设置 miniProgramConfigItem 的 setUri 预期
        $miniProgramConfigItem->expects($this->once())
            ->method('setUri')
            ->with('/admin?crudAction=index&crudControllerFqcn=WechatMiniProgramBundle%5CEntity%5CAccount')
            ->willReturnSelf();

        $adminMenu($rootItem);
    }
}
