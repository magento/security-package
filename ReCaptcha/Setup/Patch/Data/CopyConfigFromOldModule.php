<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptcha\Setup\Patch\Data;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;

/**
 * Change namespace from MageSpecialist to Magento
 */
class CopyConfigFromOldModule implements DataPatchInterface, PatchVersionInterface
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
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function apply()
    {
        $this->copyConfig([
            'msp_securitysuite_recaptcha' => 'recaptcha'
        ]);
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
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function getVersion()
    {
        return '3.0.0';
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}
