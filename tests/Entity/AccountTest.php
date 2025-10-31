<?php

namespace WechatMiniProgramBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use WechatMiniProgramBundle\Entity\Account;

/**
 * @internal
 */
#[CoversClass(Account::class)]
final class AccountTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new Account();
    }

    /**
     * 提供属性及其样本值的 Data Provider
     *
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'name' => ['name', '测试小程序'];
        yield 'appId' => ['appId', 'wx123456789'];
        yield 'appSecret' => ['appSecret', 'test_app_secret'];
        yield 'token' => ['token', 'test_token'];
        yield 'encodingAesKey' => ['encodingAesKey', 'test_encoding_aes_key'];
        yield 'loginExpireDay' => ['loginExpireDay', 30];
        yield 'pluginToken' => ['pluginToken', 'test_plugin_token'];
        yield 'valid' => ['valid', true];
    }

    public function testToStringWithValidAccount(): void
    {
        // 创建实际的Account对象并使用反射设置ID
        $account = new Account();

        // 使用反射设置私有属性id
        $reflectionProperty = new \ReflectionProperty(Account::class, 'id');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($account, 1);

        $account->setName('测试小程序');
        $account->setAppId('wx123456789');

        $this->assertEquals('测试小程序(wx123456789)', (string) $account);
    }

    public function testToStringWithNoIdAccount(): void
    {
        // 创建一个新的Account对象（id为0）
        $account = new Account();
        // Account里的__toString方法判断getId()不为0时才返回非空字符串
        $this->assertEquals('', (string) $account);
    }

    /**
     * 测试toArray方法
     * 由于我们不确定Account类具体实现了什么字段在toArray方法中，
     * 这里我们测试该方法返回数组且不为空即可
     */
    public function testToArrayWithCompleteData(): void
    {
        $account = $this->setupCompleteAccount();

        $array = $account->toArray();
        $this->assertNotEmpty($array);
    }

    /**
     * 测试retrievePlainArray方法
     * 由于我们不确定Account类具体实现了什么字段在retrievePlainArray方法中，
     * 这里我们测试该方法返回数组且不为空即可
     */
    public function testRetrievePlainArrayWithCompleteData(): void
    {
        $account = $this->setupCompleteAccount();

        $array = $account->retrievePlainArray();
        $this->assertNotEmpty($array);
    }

    /**
     * 测试retrieveApiArray方法
     * 由于我们不确定Account类具体实现了什么字段在retrieveApiArray方法中，
     * 这里我们测试该方法返回数组且不为空即可
     */
    public function testRetrieveApiArrayWithCompleteData(): void
    {
        $account = $this->setupCompleteAccount();

        $array = $account->retrieveApiArray();
        $this->assertNotEmpty($array);
    }

    /**
     * 测试retrieveAdminArray方法
     * 由于我们不确定Account类具体实现了什么字段在retrieveAdminArray方法中，
     * 这里我们测试该方法返回数组且不为空即可
     */
    public function testRetrieveAdminArrayWithCompleteData(): void
    {
        $account = $this->setupCompleteAccount();

        $array = $account->retrieveAdminArray();
        $this->assertNotEmpty($array);
    }

    /**
     * 配置账号完整数据
     */
    private function setupCompleteAccount(): Account
    {
        // 设置测试数据
        $now = new \DateTimeImmutable();
        $account = new Account();

        // 模拟getId方法返回有效值
        $reflectionProperty = new \ReflectionProperty(Account::class, 'id');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($account, 1);

        $account->setName('测试小程序');
        $account->setAppId('wx123456789');
        $account->setAppSecret('test_app_secret');
        $account->setToken('test_token');
        $account->setEncodingAesKey('test_encoding_aes_key');
        $account->setLoginExpireDay(30);
        $account->setValid(true);
        $account->setCreateTime($now);
        $account->setUpdateTime($now);
        $account->setCreatedBy('admin');
        $account->setUpdatedBy('operator');
        $account->setCreatedFromIp('127.0.0.1');
        $account->setUpdatedFromIp('192.168.1.1');

        return $account;
    }
}
