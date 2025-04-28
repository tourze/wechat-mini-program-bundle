<?php

namespace WechatMiniProgramBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use WechatMiniProgramBundle\Entity\Account;

class AccountFixture extends Fixture
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
    }
}
