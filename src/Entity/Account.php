<?php

namespace WechatMiniProgramBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\Arrayable\ApiArrayInterface;
use Tourze\Arrayable\Arrayable;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\DoctrineIpBundle\Traits\IpTraceableAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;
use Tourze\WechatMiniProgramAppIDContracts\MiniProgramInterface;
use WechatMiniProgramBundle\Repository\AccountRepository;

/**
 * @implements Arrayable<string, mixed>
 * @implements PlainArrayInterface<string, mixed>
 * @implements ApiArrayInterface<string, mixed>
 * @implements AdminArrayInterface<string, mixed>
 */
#[ORM\Entity(repositoryClass: AccountRepository::class)]
#[ORM\Table(name: 'wechat_mini_program_account', options: ['comment' => '微信小程序账户'])]
class Account implements \Stringable, Arrayable, PlainArrayInterface, ApiArrayInterface, AdminArrayInterface, MiniProgramInterface
{
    use TimestampableAware;
    use BlameableAware;
    use IpTraceableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private int $id = 0;

    #[TrackColumn]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: false, options: ['comment' => '名称'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private ?string $name = null;

    #[TrackColumn]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: false, options: ['comment' => 'AppID'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private ?string $appId = null;

    #[TrackColumn]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: false, options: ['comment' => 'AppSecret'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private ?string $appSecret = null;

    #[TrackColumn]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => 'Token'])]
    #[Assert\Length(max: 255)]
    private ?string $token = null;

    #[TrackColumn]
    #[ORM\Column(type: Types::STRING, length: 128, nullable: true, options: ['comment' => '消息加密密钥'])]
    #[Assert\Length(max: 128)]
    private ?string $encodingAesKey = null;

    #[TrackColumn]
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '登录过期天数'])]
    #[Assert\Range(min: 1, max: 365)]
    private ?int $loginExpireDay = null;

    #[TrackColumn]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '插件Token'])]
    #[Assert\Length(max: 255)]
    private ?string $pluginToken = null;

    /**
     * @var UserInterface|null 负责人
     */
    #[ORM\ManyToOne(targetEntity: UserInterface::class)]
    #[ORM\JoinColumn(nullable: true)]
    #[Assert\Valid]
    private ?UserInterface $director = null;

    #[TrackColumn]
    #[ORM\Column(type: Types::BOOLEAN, nullable: false, options: ['comment' => '是否有效', 'default' => false])]
    #[Assert\Type(type: 'bool')]
    private ?bool $valid = false;

    public function __toString(): string
    {
        if (null === $this->getName()) {
            return '';
        }

        return "{$this->getName()}({$this->getAppId()})";
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(?bool $valid): void
    {
        $this->valid = $valid;
    }

    public function getAppId(): string
    {
        return $this->appId ?? '';
    }

    public function setAppId(string $appId): void
    {
        $this->appId = $appId;
    }

    public function getAppSecret(): string
    {
        return $this->appSecret ?? '';
    }

    public function setAppSecret(string $appSecret): void
    {
        $this->appSecret = $appSecret;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): void
    {
        $this->token = $token;
    }

    public function getEncodingAesKey(): ?string
    {
        return $this->encodingAesKey;
    }

    public function setEncodingAesKey(?string $encodingAesKey): void
    {
        $this->encodingAesKey = $encodingAesKey;
    }

    public function getLoginExpireDay(): ?int
    {
        return $this->loginExpireDay;
    }

    public function setLoginExpireDay(?int $loginExpireDay): void
    {
        $this->loginExpireDay = $loginExpireDay;
    }

    public function getPluginToken(): ?string
    {
        return $this->pluginToken;
    }

    public function setPluginToken(?string $pluginToken): void
    {
        $this->pluginToken = $pluginToken;
    }

    public function getDirector(): ?UserInterface
    {
        return $this->director;
    }

    public function setDirector(?UserInterface $director): void
    {
        $this->director = $director;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'appId' => $this->getAppId(),
            'appSecret' => $this->getAppSecret(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function retrievePlainArray(): array
    {
        return [
            'id' => $this->getId(),
            'appId' => $this->getAppId(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function retrieveApiArray(): array
    {
        return [
            'id' => $this->getId(),
            'appId' => $this->getAppId(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
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
