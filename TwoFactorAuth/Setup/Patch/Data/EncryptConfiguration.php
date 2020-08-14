<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Setup\Patch\Data;

use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Encrypt the configuration
 */
class EncryptConfiguration implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var EncryptorInterface
     */
    private $encryptor;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EncryptorInterface $encryptor
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EncryptorInterface $encryptor
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->encryptor = $encryptor;
    }

    /**
     * Encrypt the config
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();

        $tfaConfigTableName = $this->moduleDataSetup->getTable('tfa_user_config');
        $connection = $this->moduleDataSetup->getConnection();

        $qry = $connection->select()->from($tfaConfigTableName);
        $configurations = $connection->fetchAll($qry);

        foreach ($configurations as $configuration) {
            if (!$this->encryptor->decrypt($configuration['encoded_config'])) {
                $connection->update(
                    $tfaConfigTableName,
                    ['encoded_config' => $this->encryptor->encrypt($configuration['encoded_config'])],
                    $connection->quoteInto('config_id = ?', $configuration['config_id'])
                );
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
