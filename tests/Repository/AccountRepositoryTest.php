<?php

declare(strict_types=1);

namespace WechatMiniProgramBundle\Tests\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use WechatMiniProgramBundle\Entity\Account;
use WechatMiniProgramBundle\Repository\AccountRepository;

/**
 * @template-extends AbstractRepositoryTestCase<Account>
 * @internal
 */
#[CoversClass(AccountRepository::class)]
#[RunTestsInSeparateProcesses]
final class AccountRepositoryTest extends AbstractRepositoryTestCase
{
    private AccountRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(AccountRepository::class);
    }

    public function testSaveAndFind(): void
    {
        $account = new Account();
        $account->setName('Test Account');
        $account->setAppId('test_app_id');
        $account->setAppSecret('test_app_secret');
        $account->setValid(true);

        $this->repository->save($account);

        $found = $this->repository->find($account->getId());
        $this->assertInstanceOf(Account::class, $found);
        $this->assertEquals('Test Account', $found->getName());
        $this->assertEquals('test_app_id', $found->getAppId());
        $this->assertEquals('test_app_secret', $found->getAppSecret());
        $this->assertTrue($found->isValid());
    }

    public function testCountWithEmptyTable(): void
    {
        $count = $this->repository->count([]);
        // @phpstan-ignore-next-line
        $this->assertIsInt($count);
        $this->assertGreaterThanOrEqual(0, $count);
    }

    public function testCountWithCriteria(): void
    {
        $account = new Account();
        $account->setName('Count Test Account');
        $account->setAppId('count_app_id');
        $account->setAppSecret('count_app_secret');
        $account->setValid(true);
        $this->repository->save($account);

        $count = $this->repository->count(['valid' => true]);
        // @phpstan-ignore-next-line
        $this->assertIsInt($count);
        $this->assertGreaterThanOrEqual(1, $count);
    }

    public function testFindByWithMatchingCriteria(): void
    {
        $account = new Account();
        $account->setName('FindBy Test Account');
        $account->setAppId('findby_app_id');
        $account->setAppSecret('findby_app_secret');
        $account->setValid(true);
        $this->repository->save($account);

        $results = $this->repository->findBy(['appId' => 'findby_app_id']);
        // @phpstan-ignore-next-line
        $this->assertIsArray($results);
        $this->assertGreaterThanOrEqual(1, count($results));

        foreach ($results as $result) {
            $this->assertInstanceOf(Account::class, $result);
            $this->assertEquals('findby_app_id', $result->getAppId());
        }
    }

    public function testFindByWithEmptyCriteriaShouldReturnAllRecords(): void
    {
        $account = new Account();
        $account->setName('Empty Criteria Test');
        $account->setAppId('empty_criteria_app_id');
        $account->setAppSecret('empty_criteria_app_secret');
        $account->setValid(true);
        $this->repository->save($account);

        $results = $this->repository->findBy([]);
        // @phpstan-ignore-next-line
        $this->assertIsArray($results);
        $this->assertGreaterThanOrEqual(1, count($results));
    }

    public function testFindByWithLimitAndOffset(): void
    {
        for ($i = 1; $i <= 5; ++$i) {
            $account = new Account();
            $account->setName("Limit Test Account {$i}");
            $account->setAppId("limit_app_id_{$i}");
            $account->setAppSecret("limit_app_secret_{$i}");
            $account->setValid(true);
            $this->repository->save($account);
        }

        $results = $this->repository->findBy([], null, 2, 1);
        // @phpstan-ignore-next-line
        $this->assertIsArray($results);
        $this->assertLessThanOrEqual(2, count($results));
    }

    public function testFindOneByWithMatchingCriteria(): void
    {
        $account = new Account();
        $account->setName('Unique Account Name');
        $account->setAppId('unique_app_id');
        $account->setAppSecret('unique_app_secret');
        $account->setValid(true);
        $this->repository->save($account);

        $found = $this->repository->findOneBy(['appId' => 'unique_app_id']);
        $this->assertInstanceOf(Account::class, $found);
        $this->assertEquals('unique_app_id', $found->getAppId());
        $this->assertEquals('Unique Account Name', $found->getName());
    }

    public function testFindOneByWithOrderBy(): void
    {
        $account1 = new Account();
        $account1->setName('Account First');
        $account1->setAppId('first_app_id');
        $account1->setAppSecret('first_app_secret');
        $account1->setValid(true);
        $this->repository->save($account1);

        $account2 = new Account();
        $account2->setName('Account Second');
        $account2->setAppId('second_app_id');
        $account2->setAppSecret('second_app_secret');
        $account2->setValid(true);
        $this->repository->save($account2);

        $found = $this->repository->findOneBy(['valid' => true], ['name' => 'ASC']);
        $this->assertInstanceOf(Account::class, $found);
    }

    public function testRemove(): void
    {
        $account = new Account();
        $account->setName('Account to Remove');
        $account->setAppId('remove_app_id');
        $account->setAppSecret('remove_app_secret');
        $account->setValid(true);

        $this->repository->save($account);
        $id = $account->getId();

        $found = $this->repository->find($id);
        $this->assertInstanceOf(Account::class, $found);

        $this->repository->remove($account);
        $removed = $this->repository->find($id);
        $this->assertNull($removed);
    }

    public function testSaveWithoutFlush(): void
    {
        $account = new Account();
        $account->setName('No Flush Account');
        $account->setAppId('no_flush_app_id');
        $account->setAppSecret('no_flush_app_secret');
        $account->setValid(true);

        $this->repository->save($account, false);
        self::getEntityManager()->flush();

        $found = $this->repository->find($account->getId());
        $this->assertInstanceOf(Account::class, $found);
        $this->assertEquals('No Flush Account', $found->getName());
    }

    public function testFindValidList(): void
    {
        $account = new Account();
        $account->setName('Valid List Test Account');
        $account->setAppId('valid_list_app_id');
        $account->setAppSecret('valid_list_app_secret');
        $account->setValid(true);
        $this->repository->save($account);

        $result = $this->repository->findValidList();
        // @phpstan-ignore-next-line
        $this->assertIsIterable($result);

        $resultArray = iterator_to_array($result);
        $this->assertGreaterThanOrEqual(1, count($resultArray));

        foreach ($resultArray as $entity) {
            $this->assertInstanceOf(Account::class, $entity);
        }
    }

    public function testFindByNullableFields(): void
    {
        $account = new Account();
        $account->setName('Nullable Fields Test');
        $account->setAppId('nullable_app_id');
        $account->setAppSecret('nullable_app_secret');
        $account->setValid(true);
        $account->setToken(null);
        $account->setEncodingAesKey(null);
        $account->setLoginExpireDay(null);
        $account->setPluginToken(null);
        $this->repository->save($account);

        $results = $this->repository->findBy(['token' => null]);
        // @phpstan-ignore-next-line
        $this->assertIsArray($results);
        $this->assertGreaterThanOrEqual(1, count($results));

        foreach ($results as $result) {
            $this->assertInstanceOf(Account::class, $result);
            $this->assertNull($result->getToken());
        }
    }

    public function testFindByMultipleNullableFields(): void
    {
        $account = new Account();
        $account->setName('Multiple Null Fields Test');
        $account->setAppId('multiple_null_app_id');
        $account->setAppSecret('multiple_null_app_secret');
        $account->setValid(true);
        $account->setToken(null);
        $account->setEncodingAesKey(null);
        $account->setLoginExpireDay(null);
        $account->setPluginToken(null);
        $this->repository->save($account);

        $results = $this->repository->findBy([
            'token' => null,
            'encodingAesKey' => null,
            'loginExpireDay' => null,
            'pluginToken' => null,
        ]);

        // @phpstan-ignore-next-line
        $this->assertIsArray($results);
        $this->assertGreaterThanOrEqual(1, count($results));

        foreach ($results as $result) {
            $this->assertInstanceOf(Account::class, $result);
            $this->assertNull($result->getToken());
            $this->assertNull($result->getEncodingAesKey());
            $this->assertNull($result->getLoginExpireDay());
            $this->assertNull($result->getPluginToken());
        }
    }

    public function testFindOneByNullField(): void
    {
        $account = new Account();
        $account->setName('Single Null Field Test');
        $account->setAppId('single_null_app_id');
        $account->setAppSecret('single_null_app_secret');
        $account->setValid(true);
        $account->setToken(null);
        $this->repository->save($account);

        $found = $this->repository->findOneBy(['token' => null, 'appId' => 'single_null_app_id']);
        $this->assertInstanceOf(Account::class, $found);
        $this->assertNull($found->getToken());
        $this->assertEquals('single_null_app_id', $found->getAppId());
    }

    public function testCountWithNullableCriteria(): void
    {
        $account = new Account();
        $account->setName('Count Null Test');
        $account->setAppId('count_null_app_id');
        $account->setAppSecret('count_null_app_secret');
        $account->setValid(true);
        $account->setToken(null);
        $this->repository->save($account);

        $count = $this->repository->count(['token' => null]);
        // @phpstan-ignore-next-line
        $this->assertIsInt($count);
        $this->assertGreaterThanOrEqual(1, $count);
    }

    public function testFindOneByWithOrderBySorting(): void
    {
        $uniquePrefix = 'sorting_test_';

        $account1 = new Account();
        $account1->setName($uniquePrefix . 'ZZZ Last Name');
        $account1->setAppId('last_app_id_sorting');
        $account1->setAppSecret('last_app_secret');
        $account1->setValid(true);
        $this->repository->save($account1);

        $account2 = new Account();
        $account2->setName($uniquePrefix . 'AAA First Name');
        $account2->setAppId('first_app_id_sorting');
        $account2->setAppSecret('first_app_secret');
        $account2->setValid(true);
        $this->repository->save($account2);

        // Test ascending order - find the first account by name
        $foundAsc = $this->repository->findOneBy(['appId' => 'first_app_id_sorting'], ['name' => 'ASC']);
        $this->assertInstanceOf(Account::class, $foundAsc);
        $this->assertEquals('first_app_id_sorting', $foundAsc->getAppId());

        // Test descending order - find the last account by name
        $foundDesc = $this->repository->findOneBy(['appId' => 'last_app_id_sorting'], ['name' => 'DESC']);
        $this->assertInstanceOf(Account::class, $foundDesc);
        $this->assertEquals('last_app_id_sorting', $foundDesc->getAppId());
    }

    public function testFindOneByOrderByLogic(): void
    {
        // Additional test to explicitly verify findOneBy ordering behavior
        $testPrefix = 'orderby_test_' . uniqid();

        $account = new Account();
        $account->setName($testPrefix . '_test_account');
        $account->setAppId($testPrefix . '_app_id');
        $account->setAppSecret($testPrefix . '_app_secret');
        $account->setValid(true);
        $this->repository->save($account);

        // Test that findOneBy respects orderBy parameter
        $found = $this->repository->findOneBy(['valid' => true], ['id' => 'DESC']);
        $this->assertInstanceOf(Account::class, $found);
    }

    public function testFindByEncodingAesKeyIsNull(): void
    {
        $account = new Account();
        $account->setName('Encoding AES Key Null Test');
        $account->setAppId('encoding_aes_key_null_app_id');
        $account->setAppSecret('encoding_aes_key_null_app_secret');
        $account->setValid(true);
        $account->setEncodingAesKey(null);
        $this->repository->save($account);

        $results = $this->repository->findBy(['encodingAesKey' => null]);
        // @phpstan-ignore-next-line
        $this->assertIsArray($results);
        $this->assertGreaterThanOrEqual(1, count($results));

        foreach ($results as $result) {
            $this->assertInstanceOf(Account::class, $result);
            $this->assertNull($result->getEncodingAesKey());
        }
    }

    public function testFindByLoginExpireDayIsNull(): void
    {
        $account = new Account();
        $account->setName('Login Expire Day Null Test');
        $account->setAppId('login_expire_day_null_app_id');
        $account->setAppSecret('login_expire_day_null_app_secret');
        $account->setValid(true);
        $account->setLoginExpireDay(null);
        $this->repository->save($account);

        $results = $this->repository->findBy(['loginExpireDay' => null]);
        // @phpstan-ignore-next-line
        $this->assertIsArray($results);
        $this->assertGreaterThanOrEqual(1, count($results));

        foreach ($results as $result) {
            $this->assertInstanceOf(Account::class, $result);
            $this->assertNull($result->getLoginExpireDay());
        }
    }

    public function testFindByPluginTokenIsNull(): void
    {
        $account = new Account();
        $account->setName('Plugin Token Null Test');
        $account->setAppId('plugin_token_null_app_id');
        $account->setAppSecret('plugin_token_null_app_secret');
        $account->setValid(true);
        $account->setPluginToken(null);
        $this->repository->save($account);

        $results = $this->repository->findBy(['pluginToken' => null]);
        // @phpstan-ignore-next-line
        $this->assertIsArray($results);
        $this->assertGreaterThanOrEqual(1, count($results));

        foreach ($results as $result) {
            $this->assertInstanceOf(Account::class, $result);
            $this->assertNull($result->getPluginToken());
        }
    }

    public function testCountEncodingAesKeyIsNull(): void
    {
        $account = new Account();
        $account->setName('Count Encoding AES Null Test');
        $account->setAppId('count_encoding_aes_null_app_id');
        $account->setAppSecret('count_encoding_aes_null_app_secret');
        $account->setValid(true);
        $account->setEncodingAesKey(null);
        $this->repository->save($account);

        $count = $this->repository->count(['encodingAesKey' => null]);
        // @phpstan-ignore-next-line
        $this->assertIsInt($count);
        $this->assertGreaterThanOrEqual(1, $count);
    }

    public function testCountLoginExpireDayIsNull(): void
    {
        $account = new Account();
        $account->setName('Count Login Expire Day Null Test');
        $account->setAppId('count_login_expire_day_null_app_id');
        $account->setAppSecret('count_login_expire_day_null_app_secret');
        $account->setValid(true);
        $account->setLoginExpireDay(null);
        $this->repository->save($account);

        $count = $this->repository->count(['loginExpireDay' => null]);
        // @phpstan-ignore-next-line
        $this->assertIsInt($count);
        $this->assertGreaterThanOrEqual(1, $count);
    }

    public function testCountPluginTokenIsNull(): void
    {
        $account = new Account();
        $account->setName('Count Plugin Token Null Test');
        $account->setAppId('count_plugin_token_null_app_id');
        $account->setAppSecret('count_plugin_token_null_app_secret');
        $account->setValid(true);
        $account->setPluginToken(null);
        $this->repository->save($account);

        $count = $this->repository->count(['pluginToken' => null]);
        // @phpstan-ignore-next-line
        $this->assertIsInt($count);
        $this->assertGreaterThanOrEqual(1, $count);
    }

    public function testFindOneByTokenIsNull(): void
    {
        $account = new Account();
        $account->setName('FindOneBy Token Null Test');
        $account->setAppId('findone_token_null_app_id');
        $account->setAppSecret('findone_token_null_app_secret');
        $account->setValid(true);
        $account->setToken(null);
        $this->repository->save($account);

        $found = $this->repository->findOneBy(['token' => null, 'appId' => 'findone_token_null_app_id']);
        $this->assertInstanceOf(Account::class, $found);
        $this->assertNull($found->getToken());
        $this->assertEquals('findone_token_null_app_id', $found->getAppId());
    }

    public function testFindOneByEncodingAesKeyIsNull(): void
    {
        $account = new Account();
        $account->setName('FindOneBy Encoding AES Key Null Test');
        $account->setAppId('findone_encoding_aes_key_null_app_id');
        $account->setAppSecret('findone_encoding_aes_key_null_app_secret');
        $account->setValid(true);
        $account->setEncodingAesKey(null);
        $this->repository->save($account);

        $found = $this->repository->findOneBy(['encodingAesKey' => null, 'appId' => 'findone_encoding_aes_key_null_app_id']);
        $this->assertInstanceOf(Account::class, $found);
        $this->assertNull($found->getEncodingAesKey());
        $this->assertEquals('findone_encoding_aes_key_null_app_id', $found->getAppId());
    }

    public function testFindOneByLoginExpireDayIsNull(): void
    {
        $account = new Account();
        $account->setName('FindOneBy Login Expire Day Null Test');
        $account->setAppId('findone_login_expire_day_null_app_id');
        $account->setAppSecret('findone_login_expire_day_null_app_secret');
        $account->setValid(true);
        $account->setLoginExpireDay(null);
        $this->repository->save($account);

        $found = $this->repository->findOneBy(['loginExpireDay' => null, 'appId' => 'findone_login_expire_day_null_app_id']);
        $this->assertInstanceOf(Account::class, $found);
        $this->assertNull($found->getLoginExpireDay());
        $this->assertEquals('findone_login_expire_day_null_app_id', $found->getAppId());
    }

    public function testFindOneByPluginTokenIsNull(): void
    {
        $account = new Account();
        $account->setName('FindOneBy Plugin Token Null Test');
        $account->setAppId('findone_plugin_token_null_app_id');
        $account->setAppSecret('findone_plugin_token_null_app_secret');
        $account->setValid(true);
        $account->setPluginToken(null);
        $this->repository->save($account);

        $found = $this->repository->findOneBy(['pluginToken' => null, 'appId' => 'findone_plugin_token_null_app_id']);
        $this->assertInstanceOf(Account::class, $found);
        $this->assertNull($found->getPluginToken());
        $this->assertEquals('findone_plugin_token_null_app_id', $found->getAppId());
    }

    public function testFindOneByOrderByLogicForSorting(): void
    {
        // 专门测试 findOneBy 的排序逻辑
        $prefix = 'findonebyorder_' . uniqid();

        $account1 = new Account();
        $account1->setName($prefix . '_account_a');
        $account1->setAppId($prefix . '_app_1');
        $account1->setAppSecret($prefix . '_secret_1');
        $account1->setValid(true);
        $this->repository->save($account1);

        $account2 = new Account();
        $account2->setName($prefix . '_account_b');
        $account2->setAppId($prefix . '_app_2');
        $account2->setAppSecret($prefix . '_secret_2');
        $account2->setValid(true);
        $this->repository->save($account2);

        // 测试 ASC 排序
        $foundAsc = $this->repository->findOneBy(['valid' => true], ['name' => 'ASC']);
        $this->assertInstanceOf(Account::class, $foundAsc);

        // 测试 DESC 排序
        $foundDesc = $this->repository->findOneBy(['valid' => true], ['name' => 'DESC']);
        $this->assertInstanceOf(Account::class, $foundDesc);

        // 验证排序效果
        $this->assertNotEquals($foundAsc->getName(), $foundDesc->getName());
    }

    public function testFindByNullableFieldsQuery(): void
    {
        // 专门测试所有可空字段的 IS NULL 查询
        $testPrefix = 'nullable_test_' . uniqid();

        $account = new Account();
        $account->setName($testPrefix . '_account');
        $account->setAppId($testPrefix . '_app_id');
        $account->setAppSecret($testPrefix . '_secret');
        $account->setValid(true);
        $account->setToken(null);
        $account->setEncodingAesKey(null);
        $account->setLoginExpireDay(null);
        $account->setPluginToken(null);
        $this->repository->save($account);

        // 测试 token IS NULL
        $results = $this->repository->findBy(['token' => null]);
        // @phpstan-ignore-next-line
        $this->assertIsArray($results);
        $this->assertGreaterThanOrEqual(1, count($results));

        // 测试 encodingAesKey IS NULL
        $results = $this->repository->findBy(['encodingAesKey' => null]);
        // @phpstan-ignore-next-line
        $this->assertIsArray($results);
        $this->assertGreaterThanOrEqual(1, count($results));

        // 测试 loginExpireDay IS NULL
        $results = $this->repository->findBy(['loginExpireDay' => null]);
        // @phpstan-ignore-next-line
        $this->assertIsArray($results);
        $this->assertGreaterThanOrEqual(1, count($results));

        // 测试 pluginToken IS NULL
        $results = $this->repository->findBy(['pluginToken' => null]);
        // @phpstan-ignore-next-line
        $this->assertIsArray($results);
        $this->assertGreaterThanOrEqual(1, count($results));
    }

    public function testCountNullableFieldsQuery(): void
    {
        // 专门测试所有可空字段的 count IS NULL 查询
        $testPrefix = 'count_nullable_' . uniqid();

        $account = new Account();
        $account->setName($testPrefix . '_account');
        $account->setAppId($testPrefix . '_app_id');
        $account->setAppSecret($testPrefix . '_secret');
        $account->setValid(true);
        $account->setToken(null);
        $account->setEncodingAesKey(null);
        $account->setLoginExpireDay(null);
        $account->setPluginToken(null);
        $this->repository->save($account);

        // 测试 count token IS NULL
        $count = $this->repository->count(['token' => null]);
        // @phpstan-ignore-next-line
        $this->assertIsInt($count);
        $this->assertGreaterThanOrEqual(1, $count);

        // 测试 count encodingAesKey IS NULL
        $count = $this->repository->count(['encodingAesKey' => null]);
        // @phpstan-ignore-next-line
        $this->assertIsInt($count);
        $this->assertGreaterThanOrEqual(1, $count);

        // 测试 count loginExpireDay IS NULL
        $count = $this->repository->count(['loginExpireDay' => null]);
        // @phpstan-ignore-next-line
        $this->assertIsInt($count);
        $this->assertGreaterThanOrEqual(1, $count);

        // 测试 count pluginToken IS NULL
        $count = $this->repository->count(['pluginToken' => null]);
        // @phpstan-ignore-next-line
        $this->assertIsInt($count);
        $this->assertGreaterThanOrEqual(1, $count);
    }

    protected function createNewEntity(): Account
    {
        $entity = new Account();

        // 设置基本字段
        $entity->setName('Test Account ' . uniqid());
        $entity->setAppId('wx' . bin2hex(random_bytes(8)));
        $entity->setAppSecret('secret' . bin2hex(random_bytes(16)));
        $entity->setValid(true);

        return $entity;
    }

    /**
     * @return ServiceEntityRepository<Account>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }
}
