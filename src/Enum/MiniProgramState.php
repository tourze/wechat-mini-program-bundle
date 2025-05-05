<?php

namespace WechatMiniProgramBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 跳转小程序类型
 */
enum MiniProgramState: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case TRIAL = 'trial';
    case DEVELOPER = 'developer';
    case FORMAL = 'formal';

    public function getLabel(): string
    {
        return match ($this) {
            self::DEVELOPER => '开发版',
            self::TRIAL => '体验版',
            self::FORMAL => '正式版',
        };
    }
}
