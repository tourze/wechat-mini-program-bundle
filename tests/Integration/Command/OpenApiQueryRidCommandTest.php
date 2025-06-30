<?php

declare(strict_types=1);

namespace WechatMiniProgramBundle\Tests\Integration\Command;

use PHPUnit\Framework\TestCase;
use WechatMiniProgramBundle\Command\OpenApiQueryRidCommand;

class OpenApiQueryRidCommandTest extends TestCase
{
    public function testCommandExists(): void
    {
        $this->assertTrue(class_exists(OpenApiQueryRidCommand::class));
    }

    public function testCommandName(): void
    {
        $this->assertSame('wechat-mini-program:open-api:query-rid', OpenApiQueryRidCommand::NAME);
    }
}