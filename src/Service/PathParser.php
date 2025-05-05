<?php

namespace WechatMiniProgramBundle\Service;

use Psr\Link\LinkInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use WechatMiniProgramBundle\Model\PathLink;

#[AsAlias(PathParserInterface::class)]
class PathParser implements PathParserInterface
{
    public function __construct(
        private readonly LoggerInterface $logger,
    )
    {
    }

    public function parsePath(string $path, array $query = []): LinkInterface
    {
        $path = trim($path, '/');
        $this->logger->debug('解析Header参数获取小程序访问信息', [
            'path' => $path,
            'query' => $query,
        ]);

        return new PathLink($path, $query);
    }
}
