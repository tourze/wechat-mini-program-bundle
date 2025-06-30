<?php

namespace WechatMiniProgramBundle\Tests\Unit\Enum;

use PHPUnit\Framework\TestCase;
use WechatMiniProgramBundle\Enum\MiniProgramState;

class MiniProgramStateTest extends TestCase
{
    public function testEnum(): void
    {
        $this->assertTrue(enum_exists(MiniProgramState::class));
        $this->assertCount(3, MiniProgramState::cases());
        $this->assertSame('trial', MiniProgramState::TRIAL->value);
        $this->assertSame('体验版', MiniProgramState::TRIAL->getLabel());
        $this->assertSame('developer', MiniProgramState::DEVELOPER->value);
        $this->assertSame('开发版', MiniProgramState::DEVELOPER->getLabel());
        $this->assertSame('formal', MiniProgramState::FORMAL->value);
        $this->assertSame('正式版', MiniProgramState::FORMAL->getLabel());
    }
}