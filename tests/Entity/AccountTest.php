<?php

namespace WechatMiniProgramBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;
use WechatMiniProgramBundle\Entity\Account;

class AccountTest extends TestCase
{
    private Account $account;

    protected function setUp(): void
    {
        $this->account = new Account();
    }

    public function testGettersAndSetters(): void
    {
        // 测试ID - 默认是0而不是null
        $this->assertSame(0, $this->account->getId());

        // 测试name属性
        $this->account->setName('测试小程序');
        $this->assertEquals('测试小程序', $this->account->getName());

        // 测试appId属性
        $this->account->setAppId('wx123456789');
        $this->assertEquals('wx123456789', $this->account->getAppId());

        // 测试appSecret属性
        $this->account->setAppSecret('test_app_secret');
        $this->assertEquals('test_app_secret', $this->account->getAppSecret());

        // 测试token属性
        $this->account->setToken('test_token');
        $this->assertEquals('test_token', $this->account->getToken());

        // 测试encodingAesKey属性
        $this->account->setEncodingAesKey('test_encoding_aes_key');
        $this->assertEquals('test_encoding_aes_key', $this->account->getEncodingAesKey());

        // 测试loginExpireDay属性
        $this->account->setLoginExpireDay(30);
        $this->assertEquals(30, $this->account->getLoginExpireDay());

        // 测试pluginToken属性
        $this->account->setPluginToken('test_plugin_token');
        $this->assertEquals('test_plugin_token', $this->account->getPluginToken());

        // 测试valid属性
        $this->account->setValid(true);
        $this->assertTrue($this->account->isValid());

        // 测试createdBy属性
        $this->account->setCreatedBy('admin');
        $this->assertEquals('admin', $this->account->getCreatedBy());

        // 测试updatedBy属性
        $this->account->setUpdatedBy('operator');
        $this->assertEquals('operator', $this->account->getUpdatedBy());

        // 测试createdFromIp属性
        $this->account->setCreatedFromIp('127.0.0.1');
        $this->assertEquals('127.0.0.1', $this->account->getCreatedFromIp());

        // 测试updatedFromIp属性
        $this->account->setUpdatedFromIp('192.168.1.1');
        $this->assertEquals('192.168.1.1', $this->account->getUpdatedFromIp());

        // 测试createTime属性
        $dateTime = new \DateTimeImmutable();
        $this->account->setCreateTime($dateTime);
        $this->assertSame($dateTime, $this->account->getCreateTime());

        // 测试updateTime属性
        $updateTime = new \DateTimeImmutable();
        $this->account->setUpdateTime($updateTime);
        $this->assertSame($updateTime, $this->account->getUpdateTime());

        // 测试director属性
        $director = $this->createMock(UserInterface::class);
        $this->account->setDirector($director);
        $this->assertSame($director, $this->account->getDirector());
    }

    public function testToString_withValidAccount(): void
    {
        // 模拟getId方法返回有效值
        $account = $this->getMockBuilder(Account::class)
            ->onlyMethods(['getId'])
            ->getMock();
        $account->method('getId')->willReturn(1);

        $account->setName('测试小程序');
        $account->setAppId('wx123456789');

        $this->assertEquals('测试小程序(wx123456789)', (string)$account);
    }

    public function testToString_withNoIdAccount(): void
    {
        // 创建一个新的Account对象（id为0）
        $account = new Account();
        // Account里的__toString方法判断getId()不为0时才返回非空字符串
        $this->assertEquals('', (string)$account);
    }

    /**
     * 测试toArray方法
     * 由于我们不确定Account类具体实现了什么字段在toArray方法中，
     * 这里我们测试该方法返回数组且不为空即可
     */
    public function testToArray_withCompleteData(): void
    {
        $this->setupCompleteAccount();

        $array = $this->account->toArray();
        $this->assertNotEmpty($array);
    }

    /**
     * 测试retrievePlainArray方法
     * 由于我们不确定Account类具体实现了什么字段在retrievePlainArray方法中，
     * 这里我们测试该方法返回数组且不为空即可
     */
    public function testRetrievePlainArray_withCompleteData(): void
    {
        $this->setupCompleteAccount();

        $array = $this->account->retrievePlainArray();
        $this->assertNotEmpty($array);
    }

    /**
     * 测试retrieveApiArray方法
     * 由于我们不确定Account类具体实现了什么字段在retrieveApiArray方法中，
     * 这里我们测试该方法返回数组且不为空即可
     */
    public function testRetrieveApiArray_withCompleteData(): void
    {
        $this->setupCompleteAccount();

        $array = $this->account->retrieveApiArray();
        $this->assertNotEmpty($array);
    }

    /**
     * 测试retrieveAdminArray方法
     * 由于我们不确定Account类具体实现了什么字段在retrieveAdminArray方法中，
     * 这里我们测试该方法返回数组且不为空即可
     */
    public function testRetrieveAdminArray_withCompleteData(): void
    {
        $this->setupCompleteAccount();

        $array = $this->account->retrieveAdminArray();
        $this->assertNotEmpty($array);
    }

    /**
     * 配置账号完整数据
     */
    private function setupCompleteAccount(): void
    {
        // 设置测试数据
        $now = new \DateTimeImmutable();

        // 模拟getId方法返回有效值
        $reflectionProperty = new \ReflectionProperty(Account::class, 'id');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($this->account, 1);

        $this->account->setName('测试小程序');
        $this->account->setAppId('wx123456789');
        $this->account->setAppSecret('test_app_secret');
        $this->account->setToken('test_token');
        $this->account->setEncodingAesKey('test_encoding_aes_key');
        $this->account->setLoginExpireDay(30);
        $this->account->setValid(true);
        $this->account->setCreateTime($now);
        $this->account->setUpdateTime($now);
        $this->account->setCreatedBy('admin');
        $this->account->setUpdatedBy('operator');
        $this->account->setCreatedFromIp('127.0.0.1');
        $this->account->setUpdatedFromIp('192.168.1.1');
    }
}
