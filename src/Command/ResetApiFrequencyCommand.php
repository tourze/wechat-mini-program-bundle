<?php

namespace WechatMiniProgramBundle\Command;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WechatMiniProgramBundle\Repository\AccountRepository;
use WechatMiniProgramBundle\Request\ResetApiFrequencyRequest;
use WechatMiniProgramBundle\Service\Client;

/**
 * openApi管理-使用AppSecret重置API调用次数
 *
 * @see https://developers.weixin.qq.com/miniprogram/dev/OpenApiDoc/openApi-mgnt/clearQuotaByAppSecret.html
 */
#[AsCommand(name: 'wechat:official-account:ResetApiFrequencyCommand', description: 'openApi管理-使用AppSecret重置API调用次数')]
class ResetApiFrequencyCommand extends Command
{
    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly Client $client,
        private readonly LoggerInterface $logger,
        ?string $name = null,
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->addArgument('account_id', InputArgument::OPTIONAL, description: '小程序account id');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $accountId = $input->getArgument('account_id');
        $currentAccount = $this->accountRepository->find($accountId);
        $request = new ResetApiFrequencyRequest();
        $request->setAppId($currentAccount->getAppId());
        $request->setAppSecret($currentAccount->getAppSecret());
        $response = $this->client->request($request);
        if (!isset($response['errcode'])) {
            $this->logger->error('AppSecret重置API调用次数错误', [
                'account' => $currentAccount,
                'response' => $response,
            ]);
        }

        return Command::SUCCESS;
    }
}
