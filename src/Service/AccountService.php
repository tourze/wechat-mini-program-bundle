<?php

namespace WechatMiniProgramBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use WechatMiniProgramBundle\Entity\Account;
use WechatMiniProgramBundle\Repository\AccountRepository;

class AccountService
{
    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * 根据请求情况，决定当前是哪个小程序Account
     */
    public function detectAccountFromRequest(?Request $request, string $appId = ''): ?Account
    {
        if ((bool) empty($appId) && null !== $request) {
            $appId = $request->headers->get('weapp-appid');
        }

        $account = null;
        if ((bool) $appId) {
            $account = $this->accountRepository->findOneBy([
                'appId' => $appId,
            ]);
        }

        // 如果只有一个，那就直接通过
        if (!$account && 1 === $this->accountRepository->count([])) {
            $list = $this->accountRepository->findBy(['valid' => true]);
            if (!empty($list)) {
                $account = $list[0];
            }
        }

        // 在特殊情况下，我们会找不到直接关联的小程序，那么走回去之前的兼容逻辑
        if (!$account && (bool) isset($_ENV['SNS_WECHAT_XCX_APP_ID'])&& $_ENV['SNS_WECHAT_XCX_APP_ID']) {
            // 再尝试下找个appid
            $account = $this->accountRepository->findOneBy([
                'appId' => $_ENV['SNS_WECHAT_XCX_APP_ID'],
            ]);
            if (!$account) {
                $account = new Account();
                $account->setName('旧小程序兼容');
                $account->setAppId($_ENV['SNS_WECHAT_XCX_APP_ID']);
                $account->setAppSecret($_ENV['SNS_WECHAT_XCX_APP_SECRET']);
                $account->setToken($_ENV['SNS_WECHAT_XCX_SERVER_TOKEN'] ?? '');
            }
            $account->setValid(true);
            $this->logger->info('创建一个临时的小程序Account', [
                'name' => $account->getName(),
                'appId' => $account->getAppId(),
                'appSecret' => $account->getAppSecret(),
            ]);
            $this->entityManager->persist($account);
            $this->entityManager->flush();
        }

        return $account;
    }
}
