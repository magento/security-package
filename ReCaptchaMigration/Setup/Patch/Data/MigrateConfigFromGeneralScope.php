<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaMigration\Setup\Patch\Data;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;

/**
 * Migrate config values from general to frontend and backend scopes.
 */
class MigrateConfigFromGeneralScope implements DataPatchInterface, PatchVersionInterface
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
     * @inheritdoc
     */
    public function apply()
    {
        $this->copyConfig(
            [
                'recaptcha/general' => 'recaptcha/frontend',
            ]
        );
        $this->copyConfig(
            [
                'recaptcha/general' => 'recaptcha/backend',
            ]
        );
    }

    /**
     * Move config values from srcPath to dstPath.
     *
     * @param array $paths
     */
    private function copyConfig(array $paths): void
    {
        foreach ($paths as $srcPath => $dstPath) {
            $value = $this->scopeConfig->getValue($srcPath);
            if (is_array($value)) {
                foreach (array_keys($value) as $v) {
                    $this->copyConfig([$srcPath . '/' . $v => $dstPath . '/' . $v]);
                }
            } else {
                $this->copyRecord($srcPath, $dstPath);
            }
        }
    }

    /**
     * Copy one config record.
     *
     * Skip if record on destination path already exists.
     *
     * @param string $srcPath
     * @param string $dstPath
     */
    private function copyRecord(string $srcPath, string $dstPath): void
    {
        $connection = $this->moduleDataSetup->getConnection();
        $configDataTable = $this->moduleDataSetup->getTable('core_config_data');

        $dstSelect = $connection->select()
            ->from($configDataTable)
            ->where('path = ?', $dstPath);

        if (!$connection->fetchOne($dstSelect)) {
            $srcSelect = $connection->select()
                ->from($configDataTable)
                ->where('path = ?', $srcPath);

            $rows = $connection->fetchAll($srcSelect);
            foreach ($rows as $row) {
                unset($row['config_id']);
                $row['path'] = $dstPath;

                $connection->insert($configDataTable, $row);
            }
        };
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
    public static function getVersion()
    {
        return '3.0.0';
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }
}
