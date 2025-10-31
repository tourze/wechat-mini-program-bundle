<?php

namespace WechatMiniProgramBundle;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use HttpClientBundle\HttpClientBundle;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;
use Tourze\DoctrineIpBundle\DoctrineIpBundle;
use Tourze\DoctrineResolveTargetEntityBundle\DependencyInjection\Compiler\ResolveTargetEntityPass;
use Tourze\DoctrineTimestampBundle\DoctrineTimestampBundle;
use Tourze\DoctrineTrackBundle\DoctrineTrackBundle;
use Tourze\DoctrineUserBundle\DoctrineUserBundle;
use Tourze\JsonRPCLogBundle\JsonRPCLogBundle;
use Tourze\WechatMiniProgramAppIDContracts\MiniProgramInterface;
use WechatMiniProgramBundle\Entity\Account;

class WechatMiniProgramBundle extends Bundle implements BundleDependencyInterface
{
    public const DEFAULT_AVATAR = 'https://thirdwx.qlogo.cn/mmopen/vi_32/XA9Dx0Gv5dT0Cqic3Ghmt7ftVU8VhicGQiaTZdMS63ojMy7yF5PmXta7DGicW0Uge53TbiakdrNdaiaTiarTaGQH4JY7Q/132';

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(
            new ResolveTargetEntityPass(MiniProgramInterface::class, Account::class),
            PassConfig::TYPE_BEFORE_OPTIMIZATION,
            1000,
        );
    }

    public static function getBundleDependencies(): array
    {
        return [
            DoctrineBundle::class => ['all' => true],
            DoctrineIpBundle::class => ['all' => true],
            DoctrineTimestampBundle::class => ['all' => true],
            DoctrineUserBundle::class => ['all' => true],
            DoctrineTrackBundle::class => ['all' => true],
            JsonRPCLogBundle::class => ['all' => true],
            HttpClientBundle::class => ['all' => true],
        ];
    }
}
