<?php

namespace WechatMiniProgramBundle\Command;

use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
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
#[AsCronTask(expression: '*/20 * * * *')]
#[AsCommand(name: self::NAME, description: '读取AccessToken')]
#[WithMonologChannel(channel: 'wechat_mini_program')]
class GetAccessTokenCommand extends Command
{
    public const NAME = 'wechat-mini-program:get-access-token';

    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly Client $client,
        private readonly LoggerInterface $logger,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->accountRepository->findBy(['valid' => true]) as $account) {
            try {
                $token = $this->client->getAccountAccessToken($account);
                $output->writeln(sprintf('<info>Access token: %s</info>', json_encode($token)));
            } catch (\Throwable $exception) {
                $this->logger->error('定时获取小程序AccessToken失败', [
                    'exception' => $exception,
                    'account' => $account,
                ]);
            }
        }

        return Command::SUCCESS;
    }
}
