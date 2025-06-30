<?php

namespace WechatMiniProgramBundle\Tests\Unit\Exception;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use WechatMiniProgramBundle\Exception\WechatApiException;

class WechatApiExceptionTest extends TestCase
{
    public function testInstanceOfRuntimeException(): void
    {
        $exception = new WechatApiException();
        $this->assertInstanceOf(RuntimeException::class, $exception);
    }

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