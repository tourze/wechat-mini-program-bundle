<?php

namespace WechatMiniProgramBundle\Procedure;

use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Model\JsonRpcRequest;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;
use Tourze\JsonRPCLogBundle\Attribute\Log;
use Tourze\JsonRPCLogBundle\Procedure\LogFormatProcedure;
use WechatMiniProgramBundle\Repository\AccountRepository;
use WechatMiniProgramBundle\Service\Client;

#[MethodTag(name: '微信小程序')]
#[MethodExpose(method: 'GetWechatMiniProgramAccessToken')]
#[MethodDoc(summary: '获取微信小程序AccessToken', description: '接入方最好控制下调用频率')]
#[Log]
class GetWechatMiniProgramAccessToken extends BaseProcedure implements LogFormatProcedure
{
    #[MethodParam(description: '应用AppID')]
    public string $appId = '';

    #[MethodParam(description: '是否需要强制刷新')]
    public bool $refresh = false;

    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly Client $client,
    ) {
    }

    public function execute(): array
    {
        if (empty($this->appId)) {
            $account = $this->accountRepository->findOneBy([], ['id' => ' DESC']);
        } else {
            $account = $this->accountRepository->findOneBy([
                'appId' => $this->appId,
            ]);
        }

        if ($account === null) {
            throw new ApiException('找不到小程序');
        }

        return $this->client->getAccountAccessToken($account, $this->refresh);
    }

    public static function getMockResult(): ?array
    {
        return [
            'access_token' => uniqid() . uniqid() . uniqid(),
        ];
    }

    public function generateFormattedLogText(JsonRpcRequest $request): string
    {
        return '获取微信小程序AccessToken';
    }
}
