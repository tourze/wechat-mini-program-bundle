<?php

declare(strict_types=1);

namespace WechatMiniProgramBundle\Tests\Integration\Repository;

use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;
use WechatMiniProgramBundle\Repository\AccountRepository;

class AccountRepositoryTest extends TestCase
{
    public function testRepositoryExists(): void
    {
        $this->assertTrue(class_exists(AccountRepository::class));
    }

    public function testExtendsEntityRepository(): void
    {
        $reflection = new \ReflectionClass(AccountRepository::class);
        $this->assertTrue($reflection->isSubclassOf(EntityRepository::class));
    }
}