<?php

namespace WechatMiniProgramBundle\Tests\Unit\Exception;

use Exception;
use PHPUnit\Framework\TestCase;
use WechatMiniProgramBundle\Exception\DecryptException;

class DecryptExceptionTest extends TestCase
{
    public function testInstanceOfException(): void
    {
        $exception = new DecryptException();
        $this->assertInstanceOf(Exception::class, $exception);
    }

    public function testExceptionWithMessage(): void
    {
        $message = 'Decrypt failed';
        $exception = new DecryptException($message);
        $this->assertSame($message, $exception->getMessage());
    }

    public function testExceptionWithMessageAndCode(): void
    {
        $message = 'Decrypt failed';
        $code = 500;
        $exception = new DecryptException($message, $code);
        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($code, $exception->getCode());
    }
}