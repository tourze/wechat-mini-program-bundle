<?php

namespace WechatMiniProgramBundle\Service;

use Monolog\Attribute\WithMonologChannel;
use Psr\Link\LinkInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;
use WechatMiniProgramBundle\Model\PathLink;

#[AsAlias(id: PathParserInterface::class)]
#[WithMonologChannel(channel: 'wechat_mini_program')]
readonly class PathParser implements PathParserInterface
{
    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @param array<string, mixed> $query
     */
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
