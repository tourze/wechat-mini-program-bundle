<?php

namespace WechatMiniProgramBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;
use WechatMiniProgramBundle\Repository\AccountRepository;
use WechatMiniProgramBundle\Service\Client;

/**
 * 这里之所以使用定时任务来获取，是为了提前调用远程接口，减少前端的等待时间
 */
#[AsCronTask('*/20 * * * *')]
#[AsCommand(name: 'wechat-mini-program:GetAccessToken', description: '读取AccessToken')]
class GetAccessTokenCommand extends Command
{
    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly Client $client,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->accountRepository->findBy(['valid' => true]) as $account) {
            $token = $this->client->getAccountAccessToken($account);
            $output->writeln(sprintf('<info>Access token: %s</info>', json_encode($token)));
        }

        return Command::SUCCESS;
    }
}
