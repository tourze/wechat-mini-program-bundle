<?php

namespace WechatMiniProgramBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;
use WechatMiniProgramBundle\Exception\DecryptException;

/**
 * @internal
 */
#[CoversClass(DecryptException::class)]
final class DecryptExceptionTest extends AbstractExceptionTestCase
{
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
