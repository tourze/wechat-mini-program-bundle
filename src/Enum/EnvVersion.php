<?php

namespace WechatMiniProgramBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 运行环境变量
 */
enum EnvVersion: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case RELEASE = 'release';
    case TRIAL = 'trial';
    case DEVELOP = 'develop';

    public function getLabel(): string
    {
        return match ($this) {
            self::RELEASE => '正式版',
            self::TRIAL => '体验版',
            self::DEVELOP => '开发版',
        };
    }
}
