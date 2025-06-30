<?php

namespace WechatMiniProgramBundle\Tests\Unit\Procedure;

use PHPUnit\Framework\TestCase;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Model\JsonRpcRequest;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;
use Tourze\JsonRPCLogBundle\Procedure\LogFormatProcedure;
use WechatMiniProgramBundle\Entity\Account;
use WechatMiniProgramBundle\Procedure\GetWechatMiniProgramAccessToken;
use WechatMiniProgramBundle\Repository\AccountRepository;
use WechatMiniProgramBundle\Service\Client;

class GetWechatMiniProgramAccessTokenTest extends TestCase
{
    private GetWechatMiniProgramAccessToken $procedure;
    private AccountRepository $accountRepository;
    private Client $client;

    protected function setUp(): void
    {
        $this->accountRepository = $this->createMock(AccountRepository::class);
        $this->client = $this->createMock(Client::class);

        $this->procedure = new GetWechatMiniProgramAccessToken(
            $this->accountRepository,
            $this->client
        );
    }

    public function testExtendsBaseProcedure(): void
    {
        $this->assertInstanceOf(BaseProcedure::class, $this->procedure);
    }

    public function testImplementsLogFormatProcedure(): void
    {
        $this->assertInstanceOf(LogFormatProcedure::class, $this->procedure);
    }

    public function testExecuteWithSpecificAppId(): void
    {
        $appId = 'wx123456789';
        $this->procedure->appId = $appId;
        
        $account = $this->createMock(Account::class);
        $accessTokenData = ['access_token' => 'test_token_123'];

        $this->accountRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['appId' => $appId])
            ->willReturn($account);

        $this->client
            ->expects($this->once())
            ->method('getAccountAccessToken')
            ->with($account, false)
            ->willReturn($accessTokenData);

        $result = $this->procedure->execute();
        $this->assertSame($accessTokenData, $result);
    }

    public function testExecuteWithoutAppIdUsesLatestAccount(): void
    {
        $this->procedure->appId = '';
        
        $account = $this->createMock(Account::class);
        $accessTokenData = ['access_token' => 'test_token_123'];

        $this->accountRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with([], ['id' => ' DESC'])
            ->willReturn($account);

        $this->client
            ->expects($this->once())
            ->method('getAccountAccessToken')
            ->with($account, false)
            ->willReturn($accessTokenData);

        $result = $this->procedure->execute();
        $this->assertSame($accessTokenData, $result);
    }

    public function testExecuteWithRefreshFlag(): void
    {
        $this->procedure->appId = 'wx123456789';
        $this->procedure->refresh = true;
        
        $account = $this->createMock(Account::class);
        $accessTokenData = ['access_token' => 'test_token_123'];

        $this->accountRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn($account);

        $this->client
            ->expects($this->once())
            ->method('getAccountAccessToken')
            ->with($account, true)
            ->willReturn($accessTokenData);

        $result = $this->procedure->execute();
        $this->assertSame($accessTokenData, $result);
    }

    public function testExecuteThrowsExceptionWhenAccountNotFound(): void
    {
        $this->procedure->appId = 'wx123456789';

        $this->accountRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null);

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('找不到小程序');

        $this->procedure->execute();
    }

    public function testGetMockResult(): void
    {
        $result = GetWechatMiniProgramAccessToken::getMockResult();
        $this->assertIsArray($result);
        $this->assertArrayHasKey('access_token', $result);
        $this->assertIsString($result['access_token']);
        $this->assertNotEmpty($result['access_token']);
    }

    public function testGenerateFormattedLogText(): void
    {
        $request = $this->createMock(JsonRpcRequest::class);
        $result = $this->procedure->generateFormattedLogText($request);
        $this->assertSame('获取微信小程序AccessToken', $result);
    }
}