<?php

namespace WechatMiniProgramBundle\Model;

use Psr\Link\LinkInterface;

class PathLink implements LinkInterface
{
    public function __construct(private readonly string $path, private readonly array $query)
    {
    }

    public function getHref(): string
    {
        return $this->path;
    }

    public function isTemplated(): bool
    {
        return false;
    }

    public function getRels(): array
    {
        return [];
    }

    public function getAttributes(): array
    {
        return $this->query;
    }
}
