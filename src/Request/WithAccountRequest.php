<?php

namespace WechatMiniProgramBundle\Request;

use HttpClientBundle\Request\ApiRequest;
use Tourze\WechatMiniProgramAppIDContracts\MiniProgramInterface;

abstract class WithAccountRequest extends ApiRequest implements AppendAccessToken
{
    /**
     * @var MiniProgramInterface 请求关联的Account信息
     */
    private MiniProgramInterface $account;

    public function getAccount(): MiniProgramInterface
    {
        return $this->account;
    }

    public function setAccount(MiniProgramInterface $account): void
    {
        $this->account = $account;
    }

    /**
     * @return array<string, mixed>
     */
    public function generateLogData(): array
    {
        $result = parent::generateLogData();
        if (null === $result) {
            $result = [];
        }

        if (isset($this->account)) {
            $result['account'] = $this->account instanceof \Stringable ? strval($this->account) : $this->account->getAppId();
        }

        return $result;
    }
}
