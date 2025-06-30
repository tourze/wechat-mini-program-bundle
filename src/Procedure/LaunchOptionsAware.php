<?php

namespace WechatMiniProgramBundle\Procedure;

use Tourze\JsonRPC\Core\Attribute\MethodParam;

trait LaunchOptionsAware
{
    #[MethodParam(description: '小程序冷启动参数')]
    public array $launchOptions = [];

    #[MethodParam(description: '小程序本次启动参数')]
    public array $enterOptions = [];
}
