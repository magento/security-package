<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Setup\Patch\Data;

use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\TwoFactorAuth\Api\UserConfigManagerInterface;
use Magento\TwoFactorAuth\Model\Provider\Engine\Google;
use Magento\User\Model\ResourceModel\User\CollectionFactory;
use Magento\User\Model\User;

/**
 * Reset the U2f data due to rewrite
 */
class EncryptGoogleSecrets implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var CollectionFactory
     */
    private $userCollectionFactory;

    /**
     * @var UserConfigManagerInterface
     */
    private $userConfigManager;

    /**
     * @var EncryptorInterface
     */
    private $encryptor;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param CollectionFactory $userCollectionFactory
     * @param UserConfigManagerInterface $userConfigManager
     * @param EncryptorInterface $encryptor
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CollectionFactory $userCollectionFactory,
        UserConfigManagerInterface $userConfigManager,
        EncryptorInterface $encryptor
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->userCollectionFactory = $userCollectionFactory;
        $this->userConfigManager = $userConfigManager;
        $this->encryptor = $encryptor;
    }

    /**
     * @inheritdoc
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();

        /** @var \Magento\User\Model\ResourceModel\User\Collection $collection */
        $collection = $this->userCollectionFactory->create();

        foreach ($collection as $user) {
            /** @var $user User */

            try {
                $config = $this->userConfigManager->getProviderConfig((int)$user->getId(), Google::CODE);
                if (empty($config) || empty($config['secret'])) {
                    continue;
                }
                $secret = $this->encryptor->encrypt($config['secret']);
                $this->userConfigManager->addProviderConfig((int)$user->getId(), Google::CODE, ['secret' => $secret]);
            } catch (NoSuchEntityException $e) {
                continue;
            }
        }

        $this->moduleDataSetup->endSetup();

        return $this;
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }
}
