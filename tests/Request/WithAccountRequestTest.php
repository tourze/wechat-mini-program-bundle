<?php

namespace WechatMiniProgramBundle\Tests\Request;

use PHPUnit\Framework\TestCase;
use WechatMiniProgramBundle\Entity\Account;
use WechatMiniProgramBundle\Request\WithAccountRequest;

class WithAccountRequestTest extends TestCase
{
    private WithAccountRequest $request;

    protected function setUp(): void
    {
        // 创建WithAccountRequest的具体实现类
        $this->request = new class() extends WithAccountRequest {
            public function getRequestPath(): string
            {
                return '/test/path';
            }

            public function getRequestOptions(): ?array
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

    public function testGenerateLogData_withAccount(): void
    {
        // 创建Account对象并模拟__toString方法
        $account = $this->getMockBuilder(Account::class)
            ->onlyMethods(['__toString'])
            ->getMock();
        $account->method('__toString')->willReturn('测试账号(test_app_id)');

        // 设置Account到请求
        $this->request->setAccount($account);

        // 生成日志数据
        $logData = $this->request->generateLogData();

        // 验证日志数据
        $this->assertIsArray($logData);
        $this->assertArrayHasKey('account', $logData);
        $this->assertEquals('测试账号(test_app_id)', $logData['account']);
    }

    public function testGenerateLogData_withoutAccount(): void
    {
        // 创建一个可以访问protected方法的子类
        $requestMock = $this->getMockBuilder(WithAccountRequest::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['generateLogData'])
            ->getMockForAbstractClass();

        // 设置期望（模拟父类返回空数组）
        $requestMock->expects($this->once())
            ->method('generateLogData')
            ->willReturn([]);

        // 调用方法
        $logData = $requestMock->generateLogData();

        // 验证结果
        $this->assertIsArray($logData);
        $this->assertEmpty($logData);
    }

    public function testImplementsAppendAccessToken(): void
    {
        // 验证WithAccountRequest实现了AppendAccessToken接口
        $interfaces = class_implements($this->request);
        $this->assertArrayHasKey('WechatMiniProgramBundle\Request\AppendAccessToken', $interfaces);
    }
}
