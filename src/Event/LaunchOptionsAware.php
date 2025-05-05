<?php

namespace WechatMiniProgramBundle\Event;

/**
 * @see https://developers.weixin.qq.com/miniprogram/dev/api/base/app/life-cycle/wx.getLaunchOptionsSync.html
 * @see https://developers.weixin.qq.com/miniprogram/dev/api/base/app/life-cycle/wx.getEnterOptionsSync.html
 */
trait LaunchOptionsAware
{
    private array $launchOptions = [];

    private array $enterOptions = [];

    public function getLaunchOptions(): array
    {
        return $this->launchOptions;
    }

    public function setLaunchOptions(array $launchOptions): void
    {
        $this->launchOptions = $launchOptions;
    }

    public function getEnterOptions(): array
    {
        return $this->enterOptions;
    }

    public function setEnterOptions(array $enterOptions): void
    {
        $this->enterOptions = $enterOptions;
    }
}
