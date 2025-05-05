<?php

namespace WechatMiniProgramBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait LaunchOptionsAware
{
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '冷启动参数'])]
    private ?array $launchOptions = [];

    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '热启动参数'])]
    private ?array $enterOptions = [];

    public function getLaunchOptions(): ?array
    {
        return $this->launchOptions;
    }

    public function setLaunchOptions(?array $launchOptions): self
    {
        $this->launchOptions = $launchOptions;

        return $this;
    }

    public function getEnterOptions(): ?array
    {
        return $this->enterOptions;
    }

    public function setEnterOptions(?array $enterOptions): self
    {
        $this->enterOptions = $enterOptions;

        return $this;
    }
}
