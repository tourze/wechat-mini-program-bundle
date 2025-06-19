<?php

namespace WechatMiniProgramBundle;

use Knp\Menu\ItemInterface;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;
use WechatMiniProgramBundle\Entity\Account;

class AdminMenu implements MenuProviderInterface
{
    public function __construct(private readonly LinkGeneratorInterface $linkGenerator)
    {
    }

    public function __invoke(ItemInterface $item): void
    {
        if ($item->getChild('微信小程序') === null) {
            $item->addChild('微信小程序', [
                'attributes' => [
                    'icon' => 'icon icon-wechat',
                ],
            ]);
        }

        //        $item->getChild('微信小程序')->addChild('数据分析')->setUri('/diy-analysis/adminGetWeappDashboard');
        //        $item->getChild('微信小程序')->addChild('实时日志')->setUri('/diy-list/adminGetWechatRealTimeLogSearchPage');
        //        $item->getChild('微信小程序')->addChild('订阅消息选择')->setUri('/diy-list/adminGetWeappTemplateList');
        //        $item->getChild('微信小程序')->addChild('广告分析')->setUri('/diy-list/adminGetWeappAdvertisementDataListPage');

        if ($item->getChild('推送管理') === null) {
            $item->addChild('推送管理');
        }
        $item->getChild('推送管理')->addChild('小程序配置')->setUri($this->linkGenerator->getCurdListPage(Account::class));
    }
}
