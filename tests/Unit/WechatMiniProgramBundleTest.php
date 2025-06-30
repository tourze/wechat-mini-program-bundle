<?php

namespace WechatMiniProgramBundle\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use WechatMiniProgramBundle\WechatMiniProgramBundle;

class WechatMiniProgramBundleTest extends TestCase
{
    public function testExtendsBundle(): void
    {
        $bundle = new WechatMiniProgramBundle();
        $this->assertInstanceOf(Bundle::class, $bundle);
    }

    public function testDefaultAvatarConstant(): void
    {
        $expectedUrl = 'https://thirdwx.qlogo.cn/mmopen/vi_32/XA9Dx0Gv5dT0Cqic3Ghmt7ftVU8VhicGQiaTZdMS63ojMy7yF5PmXta7DGicW0Uge53TbiakdrNdaiaTiarTaGQH4JY7Q/132';
        $this->assertSame($expectedUrl, WechatMiniProgramBundle::DEFAULT_AVATAR);
    }
}