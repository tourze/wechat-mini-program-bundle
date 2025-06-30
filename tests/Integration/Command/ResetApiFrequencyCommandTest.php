<?php

declare(strict_types=1);

namespace WechatMiniProgramBundle\Tests\Integration\Command;

use PHPUnit\Framework\TestCase;
use WechatMiniProgramBundle\Command\ResetApiFrequencyCommand;

class ResetApiFrequencyCommandTest extends TestCase
{
    public function testCommandExists(): void
    {
        $this->assertTrue(class_exists(ResetApiFrequencyCommand::class));
    }

    public function testCommandName(): void
    {
        $this->assertSame('wechat:official-account:reset-api-frequency', ResetApiFrequencyCommand::NAME);
    }
}