<?php

namespace WechatMiniProgramBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\Arrayable\ApiArrayInterface;
use Tourze\Arrayable\Arrayable;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\DoctrineIpBundle\Attribute\CreateIpColumn;
use Tourze\DoctrineIpBundle\Attribute\UpdateIpColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Attribute\CreatedByColumn;
use Tourze\DoctrineUserBundle\Attribute\UpdatedByColumn;
use Tourze\EasyAdmin\Attribute\Action\Listable;
use Tourze\WechatMiniProgramAppIDContracts\MiniProgramInterface;
use WechatMiniProgramBundle\Repository\AccountRepository;

#[Listable]
#[ORM\Entity(repositoryClass: AccountRepository::class)]
#[ORM\Table(name: 'wechat_mini_program_account', options: ['comment' => '微信小程序账户'])]
class Account implements \Stringable, Arrayable, PlainArrayInterface, ApiArrayInterface, AdminArrayInterface, MiniProgramInterface
{
    use TimestampableAware;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    #[TrackColumn]
    private ?string $name = null;

    #[TrackColumn]
    private ?string $appId = null;

    #[TrackColumn]
    private ?string $appSecret = null;

    #[TrackColumn]
    private ?string $token = null;

    #[TrackColumn]
    #[ORM\Column(type: Types::STRING, length: 128, nullable: true, options: ['comment' => '消息加密密钥'])]
    private ?string $encodingAesKey = null;

    #[TrackColumn]
    private ?int $loginExpireDay = null;

    #[TrackColumn]
    private ?string $pluginToken = null;

    /**
     * @var UserInterface|null 负责人
     */
    #[ORM\ManyToOne]
    private ?UserInterface $director = null;

    #[TrackColumn]
    private ?bool $valid = false;

    #[CreatedByColumn]
    private ?string $createdBy = null;

    #[UpdatedByColumn]
    private ?string $updatedBy = null;

    #[CreateIpColumn]
    private ?string $createdFromIp = null;

    #[UpdateIpColumn]
    private ?string $updatedFromIp = null;

    public function __toString(): string
    {
        if (!$this->getId()) {
            return '';
        }

        return "{$this->getName()}({$this->getAppId()})";
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(?bool $valid): self
    {
        $this->valid = $valid;

        return $this;
    }

    public function getAppId(): string
    {
        return $this->appId;
    }

    public function setAppId(string $appId): self
    {
        $this->appId = $appId;

        return $this;
    }

    public function getAppSecret(): string
    {
        return $this->appSecret;
    }

    public function setAppSecret(string $appSecret): self
    {
        $this->appSecret = $appSecret;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getEncodingAesKey(): ?string
    {
        return $this->encodingAesKey;
    }

    public function setEncodingAesKey(?string $encodingAesKey): self
    {
        $this->encodingAesKey = $encodingAesKey;

        return $this;
    }

    public function getLoginExpireDay(): ?int
    {
        return $this->loginExpireDay;
    }

    public function setLoginExpireDay(?int $loginExpireDay): self
    {
        $this->loginExpireDay = $loginExpireDay;

        return $this;
    }

    public function getPluginToken(): ?string
    {
        return $this->pluginToken;
    }

    public function setPluginToken(?string $pluginToken): self
    {
        $this->pluginToken = $pluginToken;

        return $this;
    }

    public function getDirector(): ?UserInterface
    {
        return $this->director;
    }

    public function setDirector(?UserInterface $director): static
    {
        $this->director = $director;

        return $this;
    }

    public function setCreatedBy(?string $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getCreatedBy(): ?string
    {
        return $this->createdBy;
    }

    public function setUpdatedBy(?string $updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    public function getUpdatedBy(): ?string
    {
        return $this->updatedBy;
    }

    public function getCreatedFromIp(): ?string
    {
        return $this->createdFromIp;
    }

    public function setCreatedFromIp(?string $createdFromIp): self
    {
        $this->createdFromIp = $createdFromIp;

        return $this;
    }

    public function getUpdatedFromIp(): ?string
    {
        return $this->updatedFromIp;
    }

    public function setUpdatedFromIp(?string $updatedFromIp): self
    {
        $this->updatedFromIp = $updatedFromIp;

        return $this;
    }public function toArray(): array
    {
        return [
            'appId' => $this->getAppId(),
            'appSecret' => $this->getAppSecret(),
        ];
    }

    public function retrievePlainArray(): array
    {
        return [
            'id' => $this->getId(),
            'appId' => $this->getAppId(),
        ];
    }

    public function retrieveApiArray(): array
    {
        return [
            'id' => $this->getId(),
            'appId' => $this->getAppId(),
        ];
    }

    public function retrieveAdminArray(): array
    {
        return [
            'id' => $this->getId(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
            'valid' => $this->isValid(),
            'name' => $this->getName(),
            'appId' => $this->getAppId(),
            'appSecret' => $this->getAppSecret(),
            'token' => $this->getToken(),
            'encodingAesKey' => $this->getEncodingAesKey(),
            'loginExpireDay' => $this->getLoginExpireDay(),
            'pluginToken' => $this->getPluginToken(),
        ];
    }
}
