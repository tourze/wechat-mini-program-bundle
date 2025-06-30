<?php

declare(strict_types=1);

namespace WechatMiniProgramBundle\Tests\Integration\Service;

use PHPUnit\Framework\TestCase;
use WechatMiniProgramBundle\Service\AccountService;

class AccountServiceTest extends TestCase
{
    public function testServiceExists(): void
    {
        $this->assertTrue(class_exists(AccountService::class));
    }
}