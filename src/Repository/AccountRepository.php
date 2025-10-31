<?php

namespace WechatMiniProgramBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;
use Tourze\WechatMiniProgramAppIDContracts\MiniProgramLoaderInterface;
use WechatMiniProgramBundle\Entity\Account;

/**
 * @extends ServiceEntityRepository<Account>
 */
#[Autoconfigure(public: true)]
#[AsRepository(entityClass: Account::class)]
class AccountRepository extends ServiceEntityRepository implements MiniProgramLoaderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Account::class);
    }

    public function findValidList(): iterable
    {
        return $this->findAll();
    }

    public function save(Account $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Account $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
