<?php

namespace WechatMiniProgramBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WechatMiniProgramBundle\Repository\AccountRepository;
use WechatMiniProgramBundle\Request\GetRidRequest;
use WechatMiniProgramBundle\Service\Client;

#[AsCommand(name: 'wechat-mini-program:open-api:query-rid', description: '查询rid信息')]
class OpenApiQueryRidCommand extends Command
{
    
    public const NAME = 'wechat-mini-program:open-api:query-rid';
public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly Client $client,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('account', InputArgument::REQUIRED, '账号ID')
            ->addArgument('rid', InputArgument::REQUIRED, 'rid');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $account = $this->accountRepository->find($input->getArgument('account'));
        if (!$account) {
            throw new \Exception('找不到账号信息');
        }

        $request = new GetRidRequest();
        $request->setAccount($account);
        $request->setRid($input->getArgument('rid'));
        $response = $this->client->request($request);
        // 直接打印结果出来，给开发者看的啦
        $output->writeln(json_encode($response, JSON_PRETTY_PRINT));

        return Command::SUCCESS;
    }
}
