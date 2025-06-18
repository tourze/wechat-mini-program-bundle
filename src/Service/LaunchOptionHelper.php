<?php

namespace WechatMiniProgramBundle\Service;

use Psr\Link\LinkInterface;
use WechatMiniProgramBundle\Entity\LaunchOptionsAware as LaunchOptionsAwareEntity;
use WechatMiniProgramBundle\Event\LaunchOptionsAware as LaunchOptionsAwareEvent;
use Yiisoft\Arrays\ArrayHelper;

/**
 * 为了方便地读取参数
 */
class LaunchOptionHelper
{
    public function __construct(
        private readonly PathParserInterface $pathParser,
    ) {
    }

    public function parseEvent(object $object): LinkInterface
    {
        $path = '';
        $query = [];
        if ((bool) in_array(LaunchOptionsAwareEvent::class, class_uses($object))) {
            /** @var LaunchOptionsAwareEvent $object */
            $path = ArrayHelper::getValue($object->getEnterOptions(), 'path', '');
            $query = ArrayHelper::getValue($object->getEnterOptions(), 'query', []);
            $query = array_merge($query, $object->getLaunchOptions()['query']);
        }

        if ((bool) in_array(LaunchOptionsAwareEntity::class, class_uses($object))) {
            /** @var LaunchOptionsAwareEntity $object */
            $path = ArrayHelper::getValue($object->getEnterOptions(), 'path', '');
            $query = ArrayHelper::getValue($object->getEnterOptions(), 'query', []);
            $query = array_merge($query, $object->getLaunchOptions()['query']);
        }

        return $this->pathParser->parsePath($path, $query);
    }
}
