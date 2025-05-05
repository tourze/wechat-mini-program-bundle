<?php

namespace WechatMiniProgramBundle\Request;

class GetRidRequest extends WithAccountRequest
{
    private string $rid;

    public function getRequestPath(): string
    {
        return '/cgi-bin/openapi/rid/get';
    }

    public function getRequestOptions(): ?array
    {
        $json = [
            'rid' => $this->getRid(),
        ];

        return [
            'json' => $json,
        ];
    }

    public function getRid(): string
    {
        return $this->rid;
    }

    public function setRid(string $rid): void
    {
        $this->rid = $rid;
    }
}
