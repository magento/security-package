<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Test\Integration;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\User\Model\ResourceModel\User\Collection as AdminUserCollection;
use Magento\TwoFactorAuth\Api\UserConfigManagerInterface;
use PHPUnit\Framework\TestCase;

/**
 * @magentoDbIsolation enabled
 */
class UserConfigManagerTest extends TestCase
{
    /**
     * @var UserConfigManagerInterface
     */
    private $userConfigManager;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->userConfigManager = Bootstrap::getObjectManager()->get(UserConfigManagerInterface::class);
        $this->serializer = Bootstrap::getObjectManager()->get(SerializerInterface::class);
    }

    /**
     * @magentoDataFixture Magento/User/_files/dummy_user.php
     */
    public function testShouldSetAndGetProviderConfiguration(): void
    {
        /** @var AdminUserCollection $dummyUserCollection */
        $dummyUserCollection = Bootstrap::getObjectManager()->create(AdminUserCollection::class);
        $dummyUserCollection->addFieldToFilter('username', 'dummy_username');
        $dummyUser = $dummyUserCollection->getFirstItem();

        $configPayload = ['a' => 1, 'b' => 2];

        $this->userConfigManager->setProviderConfig(
            (int)$dummyUser->getId(),
            'test_provider',
            $configPayload
        );

        $this->assertSame(
            $configPayload,
            $this->userConfigManager->getProviderConfig((int)$dummyUser->getId(), 'test_provider')
        );
    }

    /**
     * @magentoDataFixture Magento/User/_files/dummy_user.php
     */
    public function testShouldSetAndGetConfiguredProviders(): void
    {
        /** @var AdminUserCollection $dummyUserCollection */
        $dummyUserCollection = Bootstrap::getObjectManager()->create(AdminUserCollection::class);
        $dummyUserCollection->addFieldToFilter('username', 'dummy_username');
        $dummyUser = $dummyUserCollection->getFirstItem();

        $providers = ['test_provider1', 'test_provider2'];

        $this->userConfigManager->setProvidersCodes((int)$dummyUser->getId(), $providers);

        $this->assertSame(
            $providers,
            $this->userConfigManager->getProvidersCodes((int)$dummyUser->getId())
        );
    }

    /**
     * @magentoDataFixture Magento/User/_files/dummy_user.php
     */
    public function testShouldSetAndGetDefaultProvider(): void
    {
        /** @var AdminUserCollection $dummyUserCollection */
        $dummyUserCollection = Bootstrap::getObjectManager()->create(AdminUserCollection::class);
        $dummyUserCollection->addFieldToFilter('username', 'dummy_username');
        $dummyUser = $dummyUserCollection->getFirstItem();

        $provider = 'test_provider';

        $this->userConfigManager->setDefaultProvider((int)$dummyUser->getId(), $provider);

        $this->assertSame(
            $provider,
            $this->userConfigManager->getDefaultProvider((int)$dummyUser->getId())
        );
    }

    /**
     * @magentoDataFixture Magento/User/_files/dummy_user.php
     */
    public function testShouldResetProviderConfiguration(): void
    {
        /** @var AdminUserCollection $dummyUserCollection */
        $dummyUserCollection = Bootstrap::getObjectManager()->create(AdminUserCollection::class);
        $dummyUserCollection->addFieldToFilter('username', 'dummy_username');
        $dummyUser = $dummyUserCollection->getFirstItem();

        $configPayload = ['a' => 1, 'b' => 2];

        $this->userConfigManager->setProviderConfig(
            (int)$dummyUser->getId(),
            'test_provider',
            $configPayload
        );
        $this->userConfigManager->resetProviderConfig((int)$dummyUser->getId(), 'test_provider');

        $this->assertNull(
            $this->userConfigManager->getProviderConfig((int)$dummyUser->getId(), 'test_provider')
        );
    }

    /**
     * @magentoDataFixture Magento/User/_files/dummy_user.php
     */
    public function testShouldActivateProvider(): void
    {
        /** @var AdminUserCollection $dummyUserCollection */
        $dummyUserCollection = Bootstrap::getObjectManager()->create(AdminUserCollection::class);
        $dummyUserCollection->addFieldToFilter('username', 'dummy_username');
        $dummyUser = $dummyUserCollection->getFirstItem();

        $configPayload = ['a' => 1, 'b' => 2];
        $this->userConfigManager->setProviderConfig(
            (int)$dummyUser->getId(),
            'test_provider',
            $configPayload
        );

        // Check precondition
        $this->assertFalse(
            $this->userConfigManager->isProviderConfigurationActive((int)$dummyUser->getId(), 'test_provider')
        );

        $this->userConfigManager->activateProviderConfiguration((int)$dummyUser->getId(), 'test_provider');

        $this->assertTrue(
            $this->userConfigManager->isProviderConfigurationActive((int)$dummyUser->getId(), 'test_provider')
        );
    }

    /**
     * @magentoDataFixture Magento/User/_files/dummy_user.php
     */
    public function testShouldEncryptConfiguration(): void
    {
        /** @var AdminUserCollection $dummyUserCollection */
        $dummyUserCollection = Bootstrap::getObjectManager()->create(AdminUserCollection::class);
        $dummyUserCollection->addFieldToFilter('username', 'dummy_username');
        $dummyUser = $dummyUserCollection->getFirstItem();

        /** @var EncryptorInterface $encryptor */
        $encryptor = Bootstrap::getObjectManager()->create(EncryptorInterface::class);

        /** @var ResourceConnection $resourceConnection */
        $connection = Bootstrap::getObjectManager()->get(ResourceConnection::class)
            ->getConnection(ResourceConnection::DEFAULT_CONNECTION);

        $configPayload = ['a' => 1, 'b' => 2];

        $this->userConfigManager->setProviderConfig(
            (int)$dummyUser->getId(),
            'test_provider',
            $configPayload
        );

        $qry = $connection->select()
            ->from('tfa_user_config', 'encoded_config')
            ->where('user_id = ?', (int)$dummyUser->getId());

        $res = $connection->fetchOne($qry);
        $this->assertSame(
            ['test_provider' => $configPayload],
            $this->serializer->unserialize($encryptor->decrypt($res))
        );
    }

    /**
     * @magentoDataFixture Magento/User/_files/dummy_user.php
     */
    public function testShouldGetLegacyNonEncryptedProviderConfiguration(): void
    {
        /** @var AdminUserCollection $dummyUserCollection */
        $dummyUserCollection = Bootstrap::getObjectManager()->create(AdminUserCollection::class);

        /** @var ResourceConnection $resourceConnection */
        $resourceConnection = Bootstrap::getObjectManager()->get(ResourceConnection::class);
        $connection = $resourceConnection->getConnection(ResourceConnection::DEFAULT_CONNECTION);

        $dummyUserCollection->addFieldToFilter('username', 'dummy_username');
        $dummyUser = $dummyUserCollection->getFirstItem();

        $tfaUserConfig = $resourceConnection->getTableName('tfa_user_config');
        $connection = $resourceConnection->getConnection();

        $configPayload = ['a' => 1, 'b' => 2];
        $connection->insertOnDuplicate(
            $tfaUserConfig,
            [
                'encoded_config' => $this->serializer->serialize(['test_provider' => $configPayload]),
                'default_provider' => 'test_provider',
                'encoded_providers' => $this->serializer->serialize(['test_Provider']),
                'user_id' => (int)$dummyUser->getId()
            ],
            [
                'encoded_config',
                'default_provider',
                'encoded_providers'
            ]
        );

        $this->assertSame(
            $configPayload,
            $this->userConfigManager->getProviderConfig((int)$dummyUser->getId(), 'test_provider')
        );
    }

    /**
     * @magentoDataFixture Magento/User/_files/dummy_user.php
     */
    public function testShouldAddProviderConfiguration(): void
    {
        /** @var AdminUserCollection $dummyUserCollection */
        $dummyUserCollection = Bootstrap::getObjectManager()->create(AdminUserCollection::class);

        $dummyUserCollection->addFieldToFilter('username', 'dummy_username');
        $dummyUser = $dummyUserCollection->getFirstItem();

        $configPayload1 = ['a' => 1, 'b' => 2];
        $configPayload2 = ['c' => 1, 'd' => 2];
        $this->userConfigManager->addProviderConfig(
            (int)$dummyUser->getId(),
            'test_provider1',
            $configPayload1
        );
        $this->userConfigManager->addProviderConfig(
            (int)$dummyUser->getId(),
            'test_provider2',
            $configPayload2
        );

        $this->assertSame(
            $configPayload1,
            $this->userConfigManager->getProviderConfig((int)$dummyUser->getId(), 'test_provider1')
        );
        $this->assertSame(
            $configPayload2,
            $this->userConfigManager->getProviderConfig((int)$dummyUser->getId(), 'test_provider2')
        );
    }
}
