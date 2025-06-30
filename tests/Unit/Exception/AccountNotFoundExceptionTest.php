<?php

namespace WechatMiniProgramBundle\Tests\Unit\Exception;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use WechatMiniProgramBundle\Exception\AccountNotFoundException;

class AccountNotFoundExceptionTest extends TestCase
{
    public function testInstanceOfRuntimeException(): void
    {
        $exception = new AccountNotFoundException();
        $this->assertInstanceOf(RuntimeException::class, $exception);
    }

    public function testExceptionWithMessage(): void
    {
        $message = 'Account not found';
        $exception = new AccountNotFoundException($message);
        $this->assertSame($message, $exception->getMessage());
    }

    public function testExceptionWithMessageAndCode(): void
    {
        $message = 'Account not found';
        $code = 404;
        $exception = new AccountNotFoundException($message, $code);
        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($code, $exception->getCode());
    }
}