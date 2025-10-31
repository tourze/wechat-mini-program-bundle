<?php

namespace WechatMiniProgramBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;
use WechatMiniProgramBundle\Exception\AccountNotFoundException;

/**
 * @internal
 */
#[CoversClass(AccountNotFoundException::class)]
final class AccountNotFoundExceptionTest extends AbstractExceptionTestCase
{
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
