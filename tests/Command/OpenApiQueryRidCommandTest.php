<?php

namespace WechatMiniProgramBundle\Tests\Command;

use Doctrine\DBAL\LockMode;
use HttpClientBundle\Request\RequestInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;
use WechatMiniProgramBundle\Command\OpenApiQueryRidCommand;
use WechatMiniProgramBundle\Entity\Account;
use WechatMiniProgramBundle\Exception\AccountNotFoundException;
use WechatMiniProgramBundle\Repository\AccountRepository;
use WechatMiniProgramBundle\Request\GetRidRequest;
use WechatMiniProgramBundle\Service\Client;

/**
 * @internal
 */
#[CoversClass(OpenApiQueryRidCommand::class)]
#[RunTestsInSeparateProcesses]
final class OpenApiQueryRidCommandTest extends AbstractCommandTestCase
{
    private AccountRepository $accountRepository;

    private Client $client;

    protected function getCommandTester(): CommandTester
    {
        $command = self::getService(OpenApiQueryRidCommand::class);
        $this->assertInstanceOf(OpenApiQueryRidCommand::class, $command);

        return new CommandTester($command);
    }

    protected function onSetUp(): void
    {
        // 使用匿名类替代Mock对象以符合静态分析要求
        $this->accountRepository = new class extends AccountRepository {
            private ?Account $account = null;

            private bool $shouldReturnNull = false;

            /** @phpstan-ignore-next-line constructor.missingParentCall */
            public function __construct()
            {
                // 空构造函数，避免父类依赖
            }

            public function setAccount(?Account $account): void
            {
                $this->account = $account;
            }

            public function setShouldReturnNull(bool $shouldReturnNull): void
            {
                $this->shouldReturnNull = $shouldReturnNull;
            }

            /**
             * @param mixed $id
             * @param LockMode::*|int|null $lockMode
             * @param mixed $lockVersion
             */
            public function find($id, $lockMode = null, $lockVersion = null): ?object
            {
                if ($this->shouldReturnNull) {
                    return null;
                }

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

    public function testArgumentAccount(): void
    {
        $account = new class extends Account {
            public function getAppId(): string
            {
                return 'test_app_id';
            }

            public function getAppSecret(): string
            {
                return 'test_app_secret';
            }
        };
        $accountId = '1';
        $rid = 'test_rid_123';

        // @phpstan-ignore-next-line method.notFound
        $this->accountRepository->setAccount($account);
        // @phpstan-ignore-next-line method.notFound
        $this->client->setResponse(['errcode' => 0, 'errmsg' => 'ok']);

        $commandTester = $this->getCommandTester();
        $commandTester->execute([
            'account' => $accountId,
            'rid' => $rid,
        ]);

        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    public function testArgumentRid(): void
    {
        $account = new class extends Account {
            public function getAppId(): string
            {
                return 'test_app_id';
            }

            public function getAppSecret(): string
            {
                return 'test_app_secret';
            }
        };
        $accountId = '1';
        $rid = 'test_specific_rid_456';

        // @phpstan-ignore-next-line method.notFound
        $this->accountRepository->setAccount($account);
        // @phpstan-ignore-next-line method.notFound
        $this->client->setResponse(['errcode' => 0, 'errmsg' => 'ok']);

        $commandTester = $this->getCommandTester();
        $commandTester->execute([
            'account' => $accountId,
            'rid' => $rid,
        ]);

        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    public function testExecuteSuccessfully(): void
    {
        // 创建匿名Account类替代Mock对象
        $account = new class extends Account {
            public function getAppId(): string
            {
                return 'test_app_id';
            }

            public function getAppSecret(): string
            {
                return 'test_app_secret';
            }
        };

        $accountId = '1';
        $rid = 'test_rid_123';

        // @phpstan-ignore-next-line method.notFound
        $this->accountRepository->setAccount($account);

        $expectedResponse = [
            'errcode' => 0,
            'errmsg' => 'ok',
            'request' => [
                'rid' => $rid,
                'user_ip' => '127.0.0.1',
            ],
        ];

        // @phpstan-ignore-next-line method.notFound
        $this->client->setResponse($expectedResponse);

        $commandTester = $this->getCommandTester();
        $commandTester->execute([
            'account' => $accountId,
            'rid' => $rid,
        ]);

        $output = $commandTester->getDisplay();
        $jsonEncodedResponse = json_encode($expectedResponse, JSON_PRETTY_PRINT);
        $this->assertIsString($jsonEncodedResponse);
        $this->assertStringContainsString($jsonEncodedResponse, $output);
        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    public function testExecuteWithAccountNotFound(): void
    {
        $accountId = '999';
        $rid = 'test_rid_123';

        // @phpstan-ignore-next-line method.notFound
        $this->accountRepository->setShouldReturnNull(true);

        $this->expectException(AccountNotFoundException::class);
        $this->expectExceptionMessage('找不到账号信息');

        $commandTester = $this->getCommandTester();
        $commandTester->execute([
            'account' => $accountId,
            'rid' => $rid,
        ]);
    }

    public function testExecuteWithClientException(): void
    {
        // 创建匿名Account类替代Mock对象
        $account = new class extends Account {
            public function getAppId(): string
            {
                return 'test_app_id';
            }

            public function getAppSecret(): string
            {
                return 'test_app_secret';
            }
        };

        $accountId = '1';
        $rid = 'test_rid_123';

        // @phpstan-ignore-next-line method.notFound
        $this->accountRepository->setAccount($account);
        // @phpstan-ignore-next-line method.notFound
        $this->client->setShouldThrowException(true, new \RuntimeException('API request failed'));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('API request failed');

        $commandTester = $this->getCommandTester();
        $commandTester->execute([
            'account' => $accountId,
            'rid' => $rid,
        ]);
    }
}
