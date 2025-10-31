<?php

namespace WechatMiniProgramBundle\Service;

use Psr\Link\LinkInterface;

interface PathParserInterface
{
    /**
     * @param array<string, mixed> $query
     */
    public function parsePath(string $path, array $query = []): LinkInterface;
}
