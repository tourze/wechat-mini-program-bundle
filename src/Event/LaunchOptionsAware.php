<?php

declare(strict_types=1);

namespace WechatMiniProgramBundle\Event;

/**
 * 事件的 LaunchOptionsAware trait
 *
 * 提供 launchOptions 和 enterOptions 属性用于存储
 * 微信小程序启动和进入选项在事件类中。
 *
 * @phpstan-ignore trait.unused (跨包使用，PHPStan无法检测)
 */
trait LaunchOptionsAware
{
    /**
     * @var array<string, mixed>|null
     */
    private ?array $launchOptions = [];

    /**
     * @var array<string, mixed>|null
     */
    private ?array $enterOptions = [];

    /**
     * @return array<string, mixed>|null
     */
    public function getLaunchOptions(): ?array
    {
        return $this->launchOptions;
    }

    /**
     * @param array<string, mixed>|null $launchOptions
     */
    public function setLaunchOptions(?array $launchOptions): void
    {
        $this->launchOptions = $launchOptions;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getEnterOptions(): ?array
    {
        return $this->enterOptions;
    }

    /**
     * @param array<string, mixed>|null $enterOptions
     */
    public function setEnterOptions(?array $enterOptions): void
    {
        $this->enterOptions = $enterOptions;
    }
}