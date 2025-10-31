<?php

declare(strict_types=1);

namespace WechatMiniProgramBundle\Service;

use Psr\Link\LinkInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use WechatMiniProgramBundle\Entity\LaunchOptionsAware as LaunchOptionsAwareEntity;
use WechatMiniProgramBundle\Event\LaunchOptionsAware as LaunchOptionsAwareEvent;
use Yiisoft\Arrays\ArrayHelper;

/**
 * 为了方便地读取参数
 */
#[Autoconfigure(public: true)]
readonly class LaunchOptionHelper
{
    public function __construct(
        private PathParserInterface $pathParser,
    ) {
    }

    public function parseEvent(object $object): LinkInterface
    {
        $path = '';
        $query = [];
        $uses = class_uses($object);

        // 检查对象是否使用了 LaunchOptions 相关 trait
        if ($this->hasLaunchOptionsAwareTraits($uses)) {
            if (method_exists($object, 'getEnterOptions') && method_exists($object, 'getLaunchOptions')) {
                [$path, $query] = $this->extractOptionsFromObject($object);
            }
        }

        // 确保 path 是字符串类型
        $pathString = $path;

        // 确保 $query 是 string索引数组
        /** @var array<string, mixed> $stringIndexedQuery */
        $stringIndexedQuery = [];
        foreach ($query as $key => $value) {
            $stringKey = is_string($key) ? $key : (string) $key;
            $stringIndexedQuery[$stringKey] = $value;
        }

        return $this->pathParser->parsePath($pathString, $stringIndexedQuery);
    }

    /**
     * @param array<string> $uses
     */
    private function hasLaunchOptionsAwareTraits(array $uses): bool
    {
        return in_array(LaunchOptionsAwareEvent::class, $uses, true)
            || in_array(LaunchOptionsAwareEntity::class, $uses, true);
    }

    /**
     * @return array{0: string, 1: array<mixed>}
     */
    private function extractOptionsFromObject(object $object): array
    {
        // @phpstan-ignore-next-line method.notFound (checked with method_exists)
        $enterOptions = $object->getEnterOptions();

        if (!is_array($enterOptions) && !is_object($enterOptions)) {
            return ['', []];
        }

        $path = ArrayHelper::getValue($enterOptions, 'path', '');
        $pathString = is_string($path) ? $path : '';
        $enterQuery = ArrayHelper::getValue($enterOptions, 'query', []);
        $query = is_array($enterQuery) ? $enterQuery : [];

        // @phpstan-ignore-next-line method.notFound (checked with method_exists)
        $launchOptions = $object->getLaunchOptions();
        $launchQuery = is_array($launchOptions) && isset($launchOptions['query']) ? $launchOptions['query'] : [];
        $launchQuery = is_array($launchQuery) ? $launchQuery : [];
        $query = array_merge($query, $launchQuery);

        return [$pathString, $query];
    }
}