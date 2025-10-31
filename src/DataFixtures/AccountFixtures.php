<?php

namespace WechatMiniProgramBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use WechatMiniProgramBundle\Entity\Account;

#[When(env: 'test')]
#[When(env: 'dev')]
class AccountFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $account = new Account();
        $account->setName('测试小程序');
        $account->setValid(true);
        $account->setAppId('TEST2');
        $account->setAppSecret('TEST1');
        $manager->persist($account);
        $manager->flush();

        // 添加引用，供其他 Fixtures 使用
        $this->addReference(self::class, $account);
    }
}
