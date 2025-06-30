<?php

namespace WechatMiniProgramBundle\Request;

use HttpClientBundle\Request\ApiRequest;
use WechatMiniProgramBundle\Entity\Account;

abstract class WithAccountRequest extends ApiRequest implements AppendAccessToken
{
    /**
     * @var Account 请求关联的Account信息
     */
    private Account $account;

    public function getAccount(): Account
    {
        return $this->account;
    }

    public function setAccount(Account $account): void
    {
        $this->account = $account;
    }

    public function generateLogData(): array
    {
        $result = parent::generateLogData();
        if ((bool) $result && isset($this->account)) {
            $result['account'] = strval($this->account);
        }

        return $result;
    }
}
