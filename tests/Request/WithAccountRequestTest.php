<?php

namespace WechatMiniProgramBundle\Tests\Request;

use HttpClientBundle\Tests\Request\RequestTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use WechatMiniProgramBundle\Entity\Account;
use WechatMiniProgramBundle\Request\WithAccountRequest;

/**
 * @internal
 */
#[CoversClass(WithAccountRequest::class)]
final class WithAccountRequestTest extends RequestTestCase
{
    private WithAccountRequest $request;

    protected function setUp(): void
    {
        parent::setUp();

        // 创建WithAccountRequest的具体实现类
        $this->request = new class extends WithAccountRequest {
            public function getRequestPath(): string
            {
                return '/test/path';
            }

            /** @return array<string, mixed> */
            public function getRequestOptions(): array
            {
                return ['test' => 'options'];
            }
        };
    }

    public function testGetterAndSetter(): void
    {
        // 创建Account对象
        $account = new Account();
        $account->setName('测试账号');
        $account->setAppId('test_app_id');

        // 设置Account到请求
        $this->request->setAccount($account);

        // 验证获取的Account是否正确
        $this->assertSame($account, $this->request->getAccount());
    }

    public function testGenerateLogDataWithAccount(): void
    {
        // 创建真实的Account对象
        $account = new Account();

        // 使用反射设置私有属性id来支持__toString
        $reflectionProperty = new \ReflectionProperty(Account::class, 'id');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($account, 1);

        $account->setName('测试账号');
        $account->setAppId('test_app_id');

        // 设置Account到请求
        $this->request->setAccount($account);

        // 生成日志数据
        $logData = $this->request->generateLogData();

        // 验证日志数据
        $this->assertArrayHasKey('account', $logData);
        $this->assertEquals('测试账号(test_app_id)', $logData['account']);
    }

    public function testGenerateLogDataWithoutAccount(): void
    {
        // 测试没有设置account时的日志数据生成
        $logData = $this->request->generateLogData();

        // 验证结果 - 没有设置account时不应该包含account信息
        $this->assertArrayNotHasKey('account', $logData);
    }

    public function testImplementsAppendAccessToken(): void
    {
        // 验证WithAccountRequest实现了AppendAccessToken接口
        $interfaces = class_implements($this->request);
        $this->assertArrayHasKey('WechatMiniProgramBundle\Request\AppendAccessToken', $interfaces);
    }
}
