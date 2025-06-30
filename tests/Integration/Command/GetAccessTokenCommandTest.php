<?php

declare(strict_types=1);

namespace WechatMiniProgramBundle\Tests\Integration\Command;

use PHPUnit\Framework\TestCase;
use WechatMiniProgramBundle\Command\GetAccessTokenCommand;

class GetAccessTokenCommandTest extends TestCase
{
    public function testCommandExists(): void
    {
        $this->assertTrue(class_exists(GetAccessTokenCommand::class));
    }

    public function testCommandName(): void
    {
        $this->assertSame('wechat-mini-program:get-access-token', GetAccessTokenCommand::NAME);
    }
}