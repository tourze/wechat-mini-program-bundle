<?php

namespace WechatMiniProgramBundle\Service;

use Knp\Menu\ItemInterface;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;
use WechatMiniProgramBundle\Entity\Account;

readonly class AdminMenu implements MenuProviderInterface
{
    public function __construct(private LinkGeneratorInterface $linkGenerator)
    {
    }

    public function __invoke(ItemInterface $item): void
    {
        if (null === $item->getChild('微信小程序')) {
            $item->addChild('微信小程序');
        }

        //        $item->getChild('微信小程序')->addChild('数据分析')->setUri('/diy-analysis/adminGetWeappDashboard');
        //        $item->getChild('微信小程序')->addChild('实时日志')->setUri('/diy-list/adminGetWechatRealTimeLogSearchPage');
        //        $item->getChild('微信小程序')->addChild('订阅消息选择')->setUri('/diy-list/adminGetWeappTemplateList');
        //        $item->getChild('微信小程序')->addChild('广告分析')->setUri('/diy-list/adminGetWeappAdvertisementDataListPage');

        $wechatMenu = $item->getChild('微信小程序');
        if (null !== $wechatMenu) {
            $wechatMenu->addChild('小程序配置')->setUri($this->linkGenerator->getCurdListPage(Account::class));
        }
    }
}
