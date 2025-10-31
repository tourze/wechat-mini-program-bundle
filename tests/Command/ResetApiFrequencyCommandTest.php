<?php

namespace WechatMiniProgramBundle\Tests\Command;

use Doctrine\DBAL\LockMode;
use HttpClientBundle\Request\RequestInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;
use WechatMiniProgramBundle\Command\ResetApiFrequencyCommand;
use WechatMiniProgramBundle\Entity\Account;
use WechatMiniProgramBundle\Repository\AccountRepository;
use WechatMiniProgramBundle\Request\ResetApiFrequencyRequest;
use WechatMiniProgramBundle\Service\Client;

/**
 * @internal
 */
#[CoversClass(ResetApiFrequencyCommand::class)]
#[RunTestsInSeparateProcesses]
final class ResetApiFrequencyCommandTest extends AbstractCommandTestCase
{
    private AccountRepository $accountRepository;

    private Client $client;

    protected function getCommandTester(): CommandTester
    {
        $command = self::getService(ResetApiFrequencyCommand::class);
        $this->assertInstanceOf(ResetApiFrequencyCommand::class, $command);

        return new CommandTester($command);
    }

    protected function onSetUp(): void
    {
        // 使用匿名类替代Mock对象以符合静态分析要求
        $this->accountRepository = new class extends AccountRepository {
            private ?Account $account = null;

            /** @phpstan-ignore-next-line constructor.missingParentCall */
            public function __construct()
            {
                // 空构造函数，避免父类依赖
            }

            public function setAccount(?Account $account): void
            {
                $this->account = $account;
            }

            /**
             * @param mixed $id
             * @param LockMode::*|int|null $lockMode
             * @param mixed $lockVersion
             */
            public function find($id, $lockMode = null, $lockVersion = null): ?object
            {
                return $this->account;
            }
        };

        $this->client = new class extends Client {
            /** @var array<string, mixed> */
            private array $response = ['errcode' => 0, 'errmsg' => 'ok'];

            private bool $shouldThrowException = false;

            private \Exception $exceptionToThrow;

            /** @phpstan-ignore-next-line constructor.missingParentCall */
            public function __construct()
            {
                // 空构造函数，避免父类依赖
                $this->exceptionToThrow = new \RuntimeException('Test exception');
            }

            /**
             * @param array<string, mixed> $response
             */
            public function setResponse(array $response): void
            {
                $this->response = $response;
            }

            public function setShouldThrowException(bool $shouldThrow, ?\Exception $exception = null): void
            {
                $this->shouldThrowException = $shouldThrow;
                $this->exceptionToThrow = $exception ?? new \RuntimeException('Test exception');
            }

            public function request(RequestInterface $request): mixed
            {
                if ($this->shouldThrowException) {
                    throw $this->exceptionToThrow;
                }

                return $this->response;
            }
        };

        self::getContainer()->set(AccountRepository::class, $this->accountRepository);
        self::getContainer()->set(Client::class, $this->client);
    }

    public function testArgumentAccountId(): void
    {
        $account = new class extends Account {
            public function getAppId(): string
            {
                return 'wx123456789';
            }

            public function getAppSecret(): string
            {
                return 'test_app_secret';
            }
        };

        $accountId = '1';

        // @phpstan-ignore-next-line method.notFound
        $this->accountRepository->setAccount($account);
        // @phpstan-ignore-next-line method.notFound
        $this->client->setResponse(['errcode' => 0, 'errmsg' => 'ok']);

        $commandTester = $this->getCommandTester();
        $commandTester->execute([
            'account_id' => $accountId,
        ]);

        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    public function testExecuteSuccessfully(): void
    {
        // 创建匿名Account类替代Mock对象
        $account = new class extends Account {
            public function getAppId(): string
            {
                return 'wx123456789';
            }

            public function getAppSecret(): string
            {
                return 'test_app_secret';
            }
        };

        $accountId = '1';

        // @phpstan-ignore-next-line method.notFound
        $this->accountRepository->setAccount($account);

        $expectedResponse = [
            'errcode' => 0,
            'errmsg' => 'ok',
        ];

        // @phpstan-ignore-next-line method.notFound
        $this->client->setResponse($expectedResponse);

        $commandTester = $this->getCommandTester();
        $commandTester->execute([
            'account_id' => $accountId,
        ]);

        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    public function testExecuteWithErrorResponse(): void
    {
        // 创建匿名Account类替代Mock对象
        $account = new class extends Account {
            public function getAppId(): string
            {
                return 'wx123456789';
            }

            public function getAppSecret(): string
            {
                return 'test_app_secret';
            }
        };

        $accountId = '1';

        // @phpstan-ignore-next-line method.notFound
        $this->accountRepository->setAccount($account);

        $errorResponse = [
            'errcode' => 40013,
            'errmsg' => 'invalid appid',
        ];

        // @phpstan-ignore-next-line method.notFound
        $this->client->setResponse($errorResponse);

        $commandTester = $this->getCommandTester();
        $commandTester->execute([
            'account_id' => $accountId,
        ]);

        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    public function testExecuteWithNullAccount(): void
    {
        $accountId = '999';

        // @phpstan-ignore-next-line method.notFound
        $this->accountRepository->setAccount(null);

        $commandTester = $this->getCommandTester();
        $commandTester->execute([
            'account_id' => $accountId,
        ]);

        $this->assertEquals(Command::FAILURE, $commandTester->getStatusCode());
    }

    public function testExecuteWithClientException(): void
    {
        // 创建匿名Account类替代Mock对象
        $account = new class extends Account {
            public function getAppId(): string
            {
                return 'wx123456789';
            }

            public function getAppSecret(): string
            {
                return 'test_app_secret';
            }
        };

        $accountId = '1';

        // @phpstan-ignore-next-line method.notFound
        $this->accountRepository->setAccount($account);
        // @phpstan-ignore-next-line method.notFound
        $this->client->setShouldThrowException(true, new \RuntimeException('API request failed'));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('API request failed');

        $commandTester = $this->getCommandTester();
        $commandTester->execute([
            'account_id' => $accountId,
        ]);
    }
}
