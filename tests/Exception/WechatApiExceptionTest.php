<?php

namespace WechatMiniProgramBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;
use WechatMiniProgramBundle\Exception\WechatApiException;

/**
 * @internal
 */
#[CoversClass(WechatApiException::class)]
final class WechatApiExceptionTest extends AbstractExceptionTestCase
{
    public function testExceptionWithMessage(): void
    {
        $message = 'Wechat API error';
        $exception = new WechatApiException($message);
        $this->assertSame($message, $exception->getMessage());
    }

    public function testExceptionWithMessageAndCode(): void
    {
        $message = 'Wechat API error';
        $code = 40001;
        $exception = new WechatApiException($message, $code);
        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($code, $exception->getCode());
    }
}
