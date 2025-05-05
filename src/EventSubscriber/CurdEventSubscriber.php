<?php

namespace WechatMiniProgramBundle\EventSubscriber;

use AntdCpBundle\Event\BeforeDeleteRecordEvent;
use AntdCpBundle\Event\FilterQueryEvent;
use AntdCpBundle\Event\KeywordFilterEvent;
use AppBundle\Entity\BizUser;
use AppBundle\Repository\BizUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use WechatMiniProgramAuthBundle\Repository\PhoneNumberRepository;
use WechatMiniProgramAuthBundle\Repository\UserRepository;

class CurdEventSubscriber
{
    public function __construct(
        private readonly PhoneNumberRepository $phoneNumberRepository,
        private readonly BizUserRepository $bizUserRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository $userRepository,
    ) {
    }

    #[AsEventListener]
    public function onKeywordFilter(KeywordFilterEvent $event): void
    {
        if (BizUser::class !== $event->getModelClass()) {
            return;
        }

        $userIds = $this->getRelateUserIds($event->getValue());
        if (empty($userIds)) {
            return;
        }

        $event->getQueryBuilder()->orWhere('a.id IN (:appendUserId)')->setParameter('appendUserId', $userIds);
    }

    /**
     * 当后台查询用户信息时，自动附带上微信相关的用户信息查询
     */
    #[AsEventListener]
    public function onFilterQuery(FilterQueryEvent $event): void
    {
        if (!($event->getTargetEntity() instanceof BizUser)) {
            return;
        }

        $result = $event->getResult();
        $result = array_merge($result, $this->getRelateUserIds($event->getValue()));
        $result = array_unique($result);
        $result = array_values($result);
        $event->setResult($result);
    }

    /**
     * 如果删除主用户，同步删除微信用户信息
     */
    #[AsEventListener]
    public function onBeforeDeleteRecord(BeforeDeleteRecordEvent $event): void
    {
        $object = $event->getObject();
        if (!($object instanceof BizUser)) {
            return;
        }

        // $this->entityManager->lockEntity($object, function () use ($object) {
        $user = $this->userRepository->findOneBy(['openId' => $object->getUserIdentifier()]);
        if ($user) {
            $this->entityManager->remove($user);
        }

        $user = $this->userRepository->findOneBy(['unionId' => $object->getUserIdentifier()]);
        if ($user) {
            $this->entityManager->remove($user);
        }

        $this->entityManager->flush();
        // });
    }

    /**
     * 获取关键词关联的用户
     */
    protected function getRelateUserIds(?string $value): array
    {
        if (!$value) {
            return [];
        }

        $result = [];

        // 微信用户信息
        $subDQL = $this->userRepository->createQueryBuilder('u')
            ->select('u.openId') // 微信小程序OpenID，就是BizUser中的username
            ->orWhere('u.openId LIKE :value')
            ->orWhere('u.unionId LIKE :value')
            ->orWhere('u.nickName LIKE :value')
            ->setParameter('value', "%{$value}%")
            ->getDQL();
        $qb = $this->bizUserRepository->createQueryBuilder('a')
            ->select('a.id')
            ->where("a.username IN ({$subDQL})")
            ->setParameter('value', "%{$value}%");
        $result = array_merge($result, $qb->getQuery()->getSingleColumnResult());

        // 补充查询一次手机号码
        if (str_starts_with($value, '1') && 11 === mb_strlen($value)) {
            $phoneNumbers = $this->phoneNumberRepository->findBy([
                'phoneNumber' => $value,
            ]);
            foreach ($phoneNumbers as $phoneNumber) {
                foreach ($phoneNumber->getUsers() as $user) {
                    $bizUser = $this->bizUserRepository->findOneBy(['username' => $user->getOpenId()]);
                    if ($bizUser) {
                        $result[] = $bizUser->getId();
                    }
                }
            }
        }

        $result = array_unique($result);

        return array_values($result);
    }
}
