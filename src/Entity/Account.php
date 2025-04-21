<?php

namespace Tourze\WechatMiniProgramCoreBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\Arrayable\ApiArrayInterface;
use Tourze\Arrayable\Arrayable;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineIpBundle\Attribute\CreateIpColumn;
use Tourze\DoctrineIpBundle\Attribute\UpdateIpColumn;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Attribute\CreatedByColumn;
use Tourze\DoctrineUserBundle\Attribute\UpdatedByColumn;
use Tourze\EasyAdmin\Attribute\Action\Creatable;
use Tourze\EasyAdmin\Attribute\Action\Deletable;
use Tourze\EasyAdmin\Attribute\Action\Editable;
use Tourze\EasyAdmin\Attribute\Action\Listable;
use Tourze\EasyAdmin\Attribute\Column\BoolColumn;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Field\FormField;
use Tourze\EasyAdmin\Attribute\Filter\Filterable;
use Tourze\EasyAdmin\Attribute\Filter\Keyword;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;
use Tourze\WechatMiniProgramCoreBundle\Repository\AccountRepository;

#[AsPermission(title: '微信小程序账号')]
#[Listable]
#[Deletable]
#[Editable]
#[Creatable]
#[ORM\Entity(repositoryClass: AccountRepository::class)]
#[ORM\Table(name: 'wechat_mini_program_account', options: ['comment' => '微信小程序账户'])]
class Account implements \Stringable, Arrayable, PlainArrayInterface, ApiArrayInterface, AdminArrayInterface
{
    #[ListColumn(order: -1)]
    #[ExportColumn]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    #[TrackColumn]
    #[FormField(span: 18)]
    #[Keyword]
    #[ListColumn]
    #[ORM\Column(type: Types::STRING, length: 32, unique: true, options: ['comment' => '名称'])]
    private ?string $name = null;

    #[TrackColumn]
    #[FormField(span: 10)]
    #[Keyword]
    #[ListColumn]
    #[ORM\Column(type: Types::STRING, length: 64, unique: true, options: ['comment' => 'AppID'])]
    private ?string $appId = null;

    #[TrackColumn]
    #[FormField(span: 14)]
    #[Keyword]
    #[ListColumn]
    #[ORM\Column(type: Types::STRING, length: 120, options: ['comment' => 'AppSecret'])]
    private ?string $appSecret = null;

    #[TrackColumn]
    #[FormField]
    #[Keyword]
    #[ListColumn]
    #[ORM\Column(type: Types::STRING, length: 128, nullable: true, options: ['comment' => '加解密TOKEN'])]
    private ?string $token = null;

    #[TrackColumn]
    #[FormField]
    #[Keyword]
    #[ORM\Column(type: Types::STRING, length: 128, nullable: true, options: ['comment' => '消息加密密钥'])]
    private ?string $encodingAesKey = null;

    #[TrackColumn]
    #[FormField]
    #[ListColumn]
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '登录过期天数'])]
    private ?int $loginExpireDay = null;

    #[TrackColumn]
    #[ORM\Column(type: Types::STRING, length: 120, nullable: true, options: ['comment' => '插件Token'])]
    private ?string $pluginToken = null;

    /**
     * @var UserInterface|null 负责人
     */
    #[ORM\ManyToOne]
    private ?UserInterface $director = null;

    #[BoolColumn]
    #[IndexColumn]
    #[TrackColumn]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效', 'default' => 0])]
    #[ListColumn(order: 97)]
    #[FormField(order: 97)]
    private ?bool $valid = false;

    #[CreatedByColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '创建人'])]
    private ?string $createdBy = null;

    #[UpdatedByColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '更新人'])]
    private ?string $updatedBy = null;

    #[CreateIpColumn]
    #[ORM\Column(length: 128, nullable: true, options: ['comment' => '创建时IP'])]
    private ?string $createdFromIp = null;

    #[UpdateIpColumn]
    #[ORM\Column(length: 128, nullable: true, options: ['comment' => '更新时IP'])]
    private ?string $updatedFromIp = null;

    #[Filterable]
    #[IndexColumn]
    #[ListColumn(order: 98, sorter: true)]
    #[ExportColumn]
    #[CreateTimeColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '创建时间'])]
    private ?\DateTimeInterface $createTime = null;

    #[UpdateTimeColumn]
    #[ListColumn(order: 99, sorter: true)]
    #[Filterable]
    #[ExportColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '更新时间'])]
    private ?\DateTimeInterface $updateTime = null;

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

    public function getAppId(): ?string
    {
        return $this->appId;
    }

    public function setAppId(string $appId): self
    {
        $this->appId = $appId;

        return $this;
    }

    public function getAppSecret(): ?string
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
    }

    public function setCreateTime(?\DateTimeInterface $createdAt): void
    {
        $this->createTime = $createdAt;
    }

    public function getCreateTime(): ?\DateTimeInterface
    {
        return $this->createTime;
    }

    public function setUpdateTime(?\DateTimeInterface $updateTime): void
    {
        $this->updateTime = $updateTime;
    }

    public function getUpdateTime(): ?\DateTimeInterface
    {
        return $this->updateTime;
    }

    public function toArray(): array
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
