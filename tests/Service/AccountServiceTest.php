<?php

declare(strict_types=1);

namespace WechatMiniProgramBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\HttpFoundation\Request;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use WechatMiniProgramBundle\Entity\Account;
use WechatMiniProgramBundle\Service\AccountService;

/**
 * @internal
 */
#[CoversClass(AccountService::class)]
#[RunTestsInSeparateProcesses]
final class AccountServiceTest extends AbstractIntegrationTestCase
{
    private AccountService $accountService;

    protected function onSetUp(): void
    {
        $this->accountService = self::getService(AccountService::class);

        // 清理数据库中的所有Account记录，确保测试隔离
        $entityManager = self::getEntityManager();
        $entityManager->createQuery('DELETE FROM ' . Account::class)->execute();
        $entityManager->flush();
    }

    public function testDetectAccountFromRequestWithoutAppId(): void
    {
        $result = $this->accountService->detectAccountFromRequest(null);

        // 当没有 appId 且数据库中没有 Account 时，应该返回 null
        $this->assertNull($result);
    }

    public function testDetectAccountFromRequestWithAppIdHeader(): void
    {
        // 创建一个测试账户
        $testAccount = new Account();
        $testAccount->setAppId('test-app-id');
        $testAccount->setAppSecret('test-secret');
        $testAccount->setName('测试账户');
        $testAccount->setValid(true);

        $entityManager = self::getEntityManager();
        $entityManager->persist($testAccount);
        $entityManager->flush();

        // 模拟带有 weapp-appid 头的请求
        $request = new Request();
        $request->headers->set('weapp-appid', 'test-app-id');

        $result = $this->accountService->detectAccountFromRequest($request);

        $this->assertInstanceOf(Account::class, $result);
        $this->assertEquals('test-app-id', $result->getAppId());
        $this->assertEquals('测试账户', $result->getName());
    }

    public function testDetectAccountFromRequestWithAppIdParameter(): void
    {
        // 创建一个测试账户
        $testAccount = new Account();
        $testAccount->setAppId('param-app-id');
        $testAccount->setAppSecret('param-secret');
        $testAccount->setName('参数账户');
        $testAccount->setValid(true);

        $entityManager = self::getEntityManager();
        $entityManager->persist($testAccount);
        $entityManager->flush();

        $result = $this->accountService->detectAccountFromRequest(null, 'param-app-id');

        $this->assertInstanceOf(Account::class, $result);
        $this->assertEquals('param-app-id', $result->getAppId());
        $this->assertEquals('参数账户', $result->getName());
    }

    public function testDetectAccountFromRequestFallsBackToSingleAccount(): void
    {
        // 创建唯一的有效账户
        $fallbackAccount = new Account();
        $fallbackAccount->setAppId('fallback-app-id');
        $fallbackAccount->setAppSecret('fallback-secret');
        $fallbackAccount->setName('后备账户');
        $fallbackAccount->setValid(true);

        $entityManager = self::getEntityManager();
        $entityManager->persist($fallbackAccount);
        $entityManager->flush();

        // 请求一个不存在的 appId
        $request = new Request();
        $request->headers->set('weapp-appid', 'non-existent-app-id');

        $result = $this->accountService->detectAccountFromRequest($request);

        $this->assertInstanceOf(Account::class, $result);
        $this->assertEquals('fallback-app-id', $result->getAppId());
    }

    public function testDetectAccountFromRequestCreatesLegacyAccount(): void
    {
        // 保存原始环境变量
        $originalEnv = [];
        $envKeys = ['SNS_WECHAT_XCX_APP_ID', 'SNS_WECHAT_XCX_APP_SECRET', 'SNS_WECHAT_XCX_SERVER_TOKEN'];
        foreach ($envKeys as $key) {
            if (isset($_ENV[$key])) {
                $originalEnv[$key] = $_ENV[$key];
            }
        }

        // 设置环境变量以模拟旧的兼容模式
        $_ENV['SNS_WECHAT_XCX_APP_ID'] = 'legacy-app-id';
        $_ENV['SNS_WECHAT_XCX_APP_SECRET'] = 'legacy-secret';
        $_ENV['SNS_WECHAT_XCX_SERVER_TOKEN'] = 'legacy-token';

        try {
            $result = $this->accountService->detectAccountFromRequest(null);

            $this->assertInstanceOf(Account::class, $result);
            $this->assertEquals('legacy-app-id', $result->getAppId());
            $this->assertEquals('旧小程序兼容', $result->getName());
            $this->assertTrue($result->isValid());
        } finally {
            // 恢复原始环境变量
            foreach ($envKeys as $key) {
                if (isset($originalEnv[$key])) {
                    $_ENV[$key] = $originalEnv[$key];
                } else {
                    unset($_ENV[$key]);
                }
            }
        }
    }

    public function testDetectAccountFromRequestReturnsNullWhenNoFallback(): void
    {
        // 确保没有环境变量
        unset($_ENV['SNS_WECHAT_XCX_APP_ID']);

        $result = $this->accountService->detectAccountFromRequest(null);

        // 当没有任何回退选项时应该返回 null
        $this->assertNull($result);
    }
}
