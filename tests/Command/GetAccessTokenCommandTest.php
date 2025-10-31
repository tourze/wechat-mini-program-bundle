<?php

namespace WechatMiniProgramBundle\Tests\Command;

use Doctrine\DBAL\LockMode;
use HttpClientBundle\Request\RequestInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;
use Tourze\WechatMiniProgramAppIDContracts\MiniProgramInterface;
use WechatMiniProgramBundle\Command\GetAccessTokenCommand;
use WechatMiniProgramBundle\Entity\Account;
use WechatMiniProgramBundle\Repository\AccountRepository;
use WechatMiniProgramBundle\Service\Client;

/**
 * @internal
 */
#[CoversClass(GetAccessTokenCommand::class)]
#[RunTestsInSeparateProcesses]
final class GetAccessTokenCommandTest extends AbstractCommandTestCase
{
    private AccountRepository $accountRepository;

    private Client $client;

    protected function getCommandTester(): CommandTester
    {
        $command = self::getService(GetAccessTokenCommand::class);
        $this->assertInstanceOf(GetAccessTokenCommand::class, $command);

        return new CommandTester($command);
    }

    protected function onSetUp(): void
    {
        // 使用匿名类替代Mock对象以符合静态分析要求
        $this->accountRepository = new class extends AccountRepository {
            /** @var array<Account> */
            private array $accounts = [];

            private bool $shouldReturnEmpty = false;

            /** @phpstan-ignore-next-line constructor.missingParentCall */
            public function __construct()
            {
                // 空构造函数，避免父类依赖
            }

            /**
             * @param array<Account> $accounts
             */
            public function setAccounts(array $accounts): void
            {
                $this->accounts = $accounts;
            }

            public function setShouldReturnEmpty(bool $shouldReturnEmpty): void
            {
                $this->shouldReturnEmpty = $shouldReturnEmpty;
            }

            /**
             * @param array<string, mixed> $criteria
             * @param array<string, string>|null $orderBy
             * @param int|null $limit
             * @param int|null $offset
             * @return list<Account>
             */
            public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): array
            {
                if ($this->shouldReturnEmpty) {
                    return [];
                }

                return array_values($this->accounts); // 确保返回list而不是array
            }

            /**
             * @param mixed $id
             * @param LockMode::*|int|null $lockMode
             * @param mixed $lockVersion
             */
            public function find($id, $lockMode = null, $lockVersion = null): ?object
            {
                return $this->accounts[0] ?? null;
            }
        };

        $this->client = new class extends Client {
            /** @var array<string, mixed> */
            private array $accessTokenResponse = [];

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
            public function setAccessTokenResponse(array $response): void
            {
                $this->accessTokenResponse = $response;
            }

            public function setShouldThrowException(bool $shouldThrow, ?\Exception $exception = null): void
            {
                $this->shouldThrowException = $shouldThrow;
                $this->exceptionToThrow = $exception ?? new \RuntimeException('Test exception');
            }

            /**
             * @return array<string, mixed>
             */
            public function getAccountAccessToken(MiniProgramInterface $account, bool $refresh = false): array
            {
                if ($this->shouldThrowException) {
                    throw $this->exceptionToThrow;
                }

                return $this->accessTokenResponse;
            }

            public function request(RequestInterface $request): mixed
            {
                if ($this->shouldThrowException) {
                    throw $this->exceptionToThrow;
                }

                return ['errcode' => 0, 'errmsg' => 'ok'];
            }
        };

        self::getContainer()->set(AccountRepository::class, $this->accountRepository);
        self::getContainer()->set(Client::class, $this->client);
    }

    public function testExecuteWithValidAccounts(): void
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

        // @phpstan-ignore-next-line method.notFound
        $this->accountRepository->setAccounts([$account]);

        $accessToken = ['access_token' => 'test_token', 'expires_in' => 7200];
        // @phpstan-ignore-next-line method.notFound
        $this->client->setAccessTokenResponse($accessToken);

        $commandTester = $this->getCommandTester();
        $commandTester->execute([]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Access token:', $output);
        $jsonEncodedToken = json_encode($accessToken);
        $this->assertIsString($jsonEncodedToken);
        $this->assertStringContainsString($jsonEncodedToken, $output);
        $this->assertEquals(0, $commandTester->getStatusCode());
    }

    public function testExecuteWithNoValidAccounts(): void
    {
        // @phpstan-ignore-next-line method.notFound
        $this->accountRepository->setShouldReturnEmpty(true);

        $commandTester = $this->getCommandTester();
        $commandTester->execute([]);

        $this->assertEquals(0, $commandTester->getStatusCode());
    }
}
