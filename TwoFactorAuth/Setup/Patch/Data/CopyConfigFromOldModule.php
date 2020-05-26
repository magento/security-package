<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Setup\Patch\Data;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Copy configuration after migrating from MageSpecialist to Magento
 */
class CopyConfigFromOldModule implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Move config from srcPath to dstPath
     *
     * @param array $paths
     */
    private function copyConfig(array $paths): void
    {
        $connection = $this->moduleDataSetup->getConnection();
        $configDataTable = $this->moduleDataSetup->getTable('core_config_data');

        foreach ($paths as $srcPath => $dstPath) {
            $value = $this->scopeConfig->getValue($srcPath);
            if (is_array($value)) {
                foreach (array_keys($value) as $v) {
                    $this->copyConfig([$srcPath . '/' . $v => $dstPath . '/' . $v]);
                }
            } else {
                $sel = $connection->select()
                    ->from($configDataTable)
                    ->where('path = ?', $srcPath);

                $rows = $connection->fetchAll($sel);
                foreach ($rows as $row) {
                    unset($row['config_id']);
                    $row['path'] = $dstPath;

                    $connection->insert($configDataTable, $row);
                }
            }
        }
    }

    /**
     * Copy the old config
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();

        // Previous versions configuration
        $this->copyConfig([
            'msp_securitysuite_twofactorauth' => 'twofactorauth'
        ]);

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
