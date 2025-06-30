<?php

namespace WechatMiniProgramBundle\Tests\Unit;

use Knp\Menu\ItemInterface;
use PHPUnit\Framework\TestCase;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;
use WechatMiniProgramBundle\AdminMenu;
use WechatMiniProgramBundle\Entity\Account;

class AdminMenuTest extends TestCase
{
    private AdminMenu $adminMenu;
    private LinkGeneratorInterface $linkGenerator;

    protected function setUp(): void
    {
        $this->linkGenerator = $this->createMock(LinkGeneratorInterface::class);
        $this->adminMenu = new AdminMenu($this->linkGenerator);
    }

    public function testImplementsMenuProviderInterface(): void
    {
        $this->assertInstanceOf(MenuProviderInterface::class, $this->adminMenu);
    }

    public function testInvokeCreatesWechatMiniProgramMenuWhenNotExists(): void
    {
        $rootItem = $this->createMock(ItemInterface::class);
        $wechatMiniProgramItem = $this->createMock(ItemInterface::class);
        $pushManagementItem = $this->createMock(ItemInterface::class);
        $miniProgramConfigItem = $this->createMock(ItemInterface::class);

        // 模拟getChild方法调用
        $getChildCallCount = 0;
        $rootItem->method('getChild')
            ->willReturnCallback(function ($name) use (&$getChildCallCount, $pushManagementItem) {
                $getChildCallCount++;
                if ($name === '微信小程序') {
                    return null; // 第一次调用返回null
                }
                if ($name === '推送管理') {
                    if ($getChildCallCount <= 2) {
                        return null; // 第一次调用返回null
                    }
                    return $pushManagementItem; // 第二次调用返回已创建的菜单
                }
                return null;
            });

        // 模拟添加微信小程序菜单
        $rootItem->expects($this->exactly(2))
            ->method('addChild')
            ->willReturnCallback(function ($name, $options = null) use ($wechatMiniProgramItem, $pushManagementItem) {
                if ($name === '微信小程序') {
                    return $wechatMiniProgramItem;
                }
                if ($name === '推送管理') {
                    return $pushManagementItem;
                }
                return null;
            });

        // 配置链接生成器
        $this->linkGenerator->expects($this->once())
            ->method('getCurdListPage')
            ->with(Account::class)
            ->willReturn('/admin/account/list');

        // 模拟添加小程序配置子菜单
        $pushManagementItem->expects($this->once())
            ->method('addChild')
            ->with('小程序配置')
            ->willReturn($miniProgramConfigItem);

        $miniProgramConfigItem->expects($this->once())
            ->method('setUri')
            ->with('/admin/account/list')
            ->willReturnSelf();

        $this->adminMenu->__invoke($rootItem);
    }

    public function testInvokeWhenWechatMiniProgramMenuExists(): void
    {
        $rootItem = $this->createMock(ItemInterface::class);
        $wechatMiniProgramItem = $this->createMock(ItemInterface::class);
        $pushManagementItem = $this->createMock(ItemInterface::class);
        $miniProgramConfigItem = $this->createMock(ItemInterface::class);

        // 模拟getChild方法调用
        $getChildCallCount = 0;
        $rootItem->method('getChild')
            ->willReturnCallback(function ($name) use (&$getChildCallCount, $wechatMiniProgramItem, $pushManagementItem) {
                $getChildCallCount++;
                if ($name === '微信小程序') {
                    return $wechatMiniProgramItem; // 已存在
                }
                if ($name === '推送管理') {
                    if ($getChildCallCount <= 2) {
                        return null; // 第一次调用返回null
                    }
                    return $pushManagementItem; // 第二次调用返回已创建的菜单
                }
                return null;
            });

        // 只添加推送管理菜单，不添加微信小程序菜单
        $rootItem->expects($this->once())
            ->method('addChild')
            ->with('推送管理')
            ->willReturn($pushManagementItem);

        // 配置链接生成器
        $this->linkGenerator->expects($this->once())
            ->method('getCurdListPage')
            ->with(Account::class)
            ->willReturn('/admin/account/list');

        // 模拟添加小程序配置子菜单
        $pushManagementItem->expects($this->once())
            ->method('addChild')
            ->with('小程序配置')
            ->willReturn($miniProgramConfigItem);

        $miniProgramConfigItem->expects($this->once())
            ->method('setUri')
            ->with('/admin/account/list')
            ->willReturnSelf();

        $this->adminMenu->__invoke($rootItem);
    }
}