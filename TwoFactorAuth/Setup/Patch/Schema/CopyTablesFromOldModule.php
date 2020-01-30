<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Setup\Patch\Schema;

use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Copy table contents after migrating from MageSpecialist to Magento
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CopyTablesFromOldModule implements SchemaPatchInterface
{
    /**
     * @var SchemaSetupInterface
     */
    private $schemaSetup;

    /**
     * @param SchemaSetupInterface $schemaSetup
     */
    public function __construct(
        SchemaSetupInterface $schemaSetup
    ) {
        $this->schemaSetup = $schemaSetup;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function apply()
    {
        $this->schemaSetup->startSetup();

        $connection = $this->schemaSetup->getConnection();
        $sourceUserConfigTable = $this->schemaSetup->getTable('msp_tfa_user_config');
        $sourceTrustedDevicesTable = $this->schemaSetup->getTable('msp_tfa_trusted');

        $userConfigTable = $this->schemaSetup->getTable('tfa_user_config');
        $trustedDevicesTable = $this->schemaSetup->getTable('tfa_trusted');

        if ($connection->isTableExists($sourceUserConfigTable)) {
            $cols = ['user_id', 'encoded_providers', 'encoded_config', 'default_provider'];
            $connection->query($connection->insertFromSelect(
                $connection->select()->from($sourceUserConfigTable, $cols),
                $userConfigTable,
                $cols
            ));
        }

        if ($connection->isTableExists($sourceTrustedDevicesTable)) {
            $cols = ['date_time', 'user_id', 'device_name', 'token', 'last_ip'];
            $connection->query($connection->insertFromSelect(
                $connection->select()->from($sourceTrustedDevicesTable, $cols),
                $trustedDevicesTable,
                $cols
            ));
        }

        $this->schemaSetup->endSetup();
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}
