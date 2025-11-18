<?php

namespace WechatMiniProgramBundle\Tests\Controller\Admin;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;
use WechatMiniProgramBundle\Controller\Admin\AccountCrudController;
use WechatMiniProgramBundle\Entity\Account;

/**
 * @internal
 */
#[CoversClass(AccountCrudController::class)]
#[RunTestsInSeparateProcesses]
final class AccountCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    protected function getControllerService(): AccountCrudController
    {
        return new AccountCrudController();
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID header' => ['ID'];
        yield 'name header' => ['名称'];
        yield 'appId header' => ['AppID'];
        yield 'director header' => ['负责人'];
        yield 'valid header' => ['是否有效'];
        yield 'createTime header' => ['创建时间'];
        yield 'updateTime header' => ['更新时间'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        yield 'name field' => ['name'];
        yield 'appId field' => ['appId'];
        yield 'appSecret field' => ['appSecret'];
        yield 'director field' => ['director'];
        yield 'valid field' => ['valid'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        yield 'name field' => ['name'];
        yield 'appId field' => ['appId'];
        yield 'appSecret field' => ['appSecret'];
        yield 'director field' => ['director'];
        yield 'valid field' => ['valid'];
    }

    public function testAuthorizedAccessToIndex(): void
    {
        $client = self::createClientWithDatabase();
        self::getClient($client);
        $this->loginAsAdmin($client);

        $client->request('GET', '/admin/wechat-mini-program/account');

        $this->assertResponseIsSuccessful();
    }

    public function testSearchByName(): void
    {
        $client = self::createClientWithDatabase();
        self::getClient($client);
        $this->loginAsAdmin($client);

        $client->request('GET', '/admin/wechat-mini-program/account', [
            'filters' => [
                'name' => [
                    'comparison' => '=',
                    'value' => 'test-name',
                ],
            ],
        ]);

        $this->assertResponseIsSuccessful();
    }

    public function testSearchByAppId(): void
    {
        $client = self::createClientWithDatabase();
        self::getClient($client);
        $this->loginAsAdmin($client);

        $client->request('GET', '/admin/wechat-mini-program/account', [
            'filters' => [
                'appId' => [
                    'comparison' => '=',
                    'value' => 'wx123456789',
                ],
            ],
        ]);

        $this->assertResponseIsSuccessful();
    }

    public function testSearchByValidStatus(): void
    {
        $client = self::createClientWithDatabase();
        self::getClient($client);
        $this->loginAsAdmin($client);

        $client->request('GET', '/admin/wechat-mini-program/account', [
            'filters' => [
                'valid' => [
                    'value' => 'true',
                ],
            ],
        ]);

        $this->assertResponseIsSuccessful();
    }

    public function testCombinedFilters(): void
    {
        $client = self::createClientWithDatabase();
        self::getClient($client);
        $this->loginAsAdmin($client);

        $client->request('GET', '/admin/wechat-mini-program/account', [
            'filters' => [
                'name' => [
                    'comparison' => 'like',
                    'value' => 'test',
                ],
                'valid' => [
                    'value' => 'true',
                ],
            ],
        ]);

        $this->assertResponseIsSuccessful();
    }

    public function testIndexPageStructure(): void
    {
        $client = self::createClientWithDatabase();
        self::getClient($client);
        $this->loginAsAdmin($client);

        $client->request('GET', '/admin/wechat-mini-program/account');

        $this->assertResponseIsSuccessful();
    }

    public function testSearchFieldsConfiguration(): void
    {
        $client = self::createClientWithDatabase();
        self::getClient($client);
        $this->loginAsAdmin($client);

        $client->request('GET', '/admin/wechat-mini-program/account', [
            'query' => 'search-term',
        ]);

        $this->assertResponseIsSuccessful();
    }
}
