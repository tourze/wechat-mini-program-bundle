<?php

namespace WechatMiniProgramBundle\Service;

use Psr\Link\LinkInterface;

interface PathParserInterface
{
    public function parsePath(string $path, array $query = []): LinkInterface;
}
