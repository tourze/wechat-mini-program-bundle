<?php

namespace WechatMiniProgramBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\HttpFoundation\Request;
use WechatMiniProgramBundle\Entity\Account;
use WechatMiniProgramBundle\Repository\AccountRepository;

#[Autoconfigure(public: true)]
#[WithMonologChannel(channel: 'wechat_mini_program')]
readonly class AccountService
{
    public function __construct(
        private AccountRepository $accountRepository,
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * 根据请求情况，决定当前是哪个小程序Account
     */
    public function detectAccountFromRequest(?Request $request, string $appId = ''): ?Account
    {
        $appId = $this->extractAppId($request, $appId);

        $account = $this->findAccountByAppId($appId);

        if (null === $account) {
            $account = $this->findFallbackAccount();
        }

        if (null === $account) {
            $account = $this->createLegacyCompatibilityAccount();
        }

        return $account;
    }

    private function extractAppId(?Request $request, string $appId): string
    {
        if ('' === $appId && null !== $request) {
            $appId = $request->headers->get('weapp-appid', '') ?? '';
        }

        return $appId;
    }

    private function findAccountByAppId(string $appId): ?Account
    {
        if ('' === $appId) {
            return null;
        }

        return $this->accountRepository->findOneBy(['appId' => $appId]);
    }

    private function findFallbackAccount(): ?Account
    {
        // 如果只有一个，那就直接通过
        if (1 !== $this->accountRepository->count([])) {
            return null;
        }

        $list = $this->accountRepository->findBy(['valid' => true]);

        return [] !== $list ? $list[0] : null;
    }

    private function createLegacyCompatibilityAccount(): ?Account
    {
        // 在特殊情况下，我们会找不到直接关联的小程序，那么走回去之前的兼容逻辑
        if (!isset($_ENV['SNS_WECHAT_XCX_APP_ID']) || '' === $_ENV['SNS_WECHAT_XCX_APP_ID']) {
            return null;
        }

        $account = $this->accountRepository->findOneBy([
            'appId' => $_ENV['SNS_WECHAT_XCX_APP_ID'],
        ]);

        if (null === $account) {
            $account = $this->createNewLegacyAccount();
        }

        $account->setValid(true);
        $this->logAccountCreation($account);
        $this->entityManager->persist($account);
        $this->entityManager->flush();

        return $account;
    }

    private function createNewLegacyAccount(): Account
    {
        $account = new Account();
        $account->setName('旧小程序兼容');
        $account->setAppId($this->getEnvAsString('SNS_WECHAT_XCX_APP_ID'));
        $account->setAppSecret($this->getEnvAsString('SNS_WECHAT_XCX_APP_SECRET'));
        $account->setToken($this->getEnvAsString('SNS_WECHAT_XCX_SERVER_TOKEN', ''));

        return $account;
    }

    private function logAccountCreation(Account $account): void
    {
        $this->logger->info('创建一个临时的小程序Account', [
            'name' => $account->getName(),
            'appId' => $account->getAppId(),
            'appSecret' => $account->getAppSecret(),
        ]);
    }

    /**
     * 安全地从环境变量获取字符串值
     */
    private function getEnvAsString(string $key, string $default = ''): string
    {
        $value = $_ENV[$key] ?? null;

        if (null === $value) {
            return $default;
        }

        if (is_string($value)) {
            return $value;
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        return $default;
    }
}
