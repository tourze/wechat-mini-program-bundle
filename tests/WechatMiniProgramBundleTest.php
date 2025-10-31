<?php

declare(strict_types=1);

namespace WechatMiniProgramBundle\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;
use WechatMiniProgramBundle\WechatMiniProgramBundle;

/**
 * @internal
 */
#[CoversClass(WechatMiniProgramBundle::class)]
#[RunTestsInSeparateProcesses]
final class WechatMiniProgramBundleTest extends AbstractBundleTestCase
{
}
