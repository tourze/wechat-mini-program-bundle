<?php

namespace WechatMiniProgramBundle\Tests\Unit\Enum;

use PHPUnit\Framework\TestCase;
use WechatMiniProgramBundle\Enum\EnvVersion;

class EnvVersionTest extends TestCase
{
    public function testEnum(): void
    {
        $this->assertTrue(enum_exists(EnvVersion::class));
        $this->assertCount(3, EnvVersion::cases());
        $this->assertSame('release', EnvVersion::RELEASE->value);
        $this->assertSame('正式版', EnvVersion::RELEASE->getLabel());
        $this->assertSame('trial', EnvVersion::TRIAL->value);
        $this->assertSame('体验版', EnvVersion::TRIAL->getLabel());
        $this->assertSame('develop', EnvVersion::DEVELOP->value);
        $this->assertSame('开发版', EnvVersion::DEVELOP->getLabel());
    }
}