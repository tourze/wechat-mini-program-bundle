<?php

namespace WechatMiniProgramBundle\Model;

use Psr\Link\LinkInterface;

class PathLink implements LinkInterface
{
    /**
     * @param array<string, mixed> $query
     */
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

    /**
     * @return array<int, string>
     */
    public function getRels(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    public function getAttributes(): array
    {
        return $this->query;
    }
}
