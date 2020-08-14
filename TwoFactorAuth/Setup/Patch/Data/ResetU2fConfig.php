<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Setup\Patch\Data;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\TwoFactorAuth\Api\UserConfigManagerInterface;
use Magento\TwoFactorAuth\Model\Provider\Engine\U2fKey;
use Magento\User\Model\ResourceModel\User\CollectionFactory;
use Magento\User\Model\User;

/**
 * Reset the U2f data due to rewrite
 */
class ResetU2fConfig implements DataPatchInterface
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
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param CollectionFactory $userCollectionFactory
     * @param UserConfigManagerInterface $userConfigManager
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CollectionFactory $userCollectionFactory,
        UserConfigManagerInterface $userConfigManager
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->userCollectionFactory = $userCollectionFactory;
        $this->userConfigManager = $userConfigManager;
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
                $this->userConfigManager->setProviderConfig((int)$user->getId(), U2fKey::CODE, []);
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
