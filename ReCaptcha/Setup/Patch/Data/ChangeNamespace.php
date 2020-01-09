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
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ChangeNamespace implements DataPatchInterface, PatchVersionInterface
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
        // Previous versions configuration
        $this->moveConfig([
            'msp_securitysuite/recaptcha' => 'msp_securitysuite_recaptcha/general'
        ]);
        $this->moveConfig([
            'msp_securitysuite_recaptcha/general/enabled_frontend' => 'msp_securitysuite_recaptcha/frontend/enabled',
            'msp_securitysuite_recaptcha/general/enabled_backend' => 'msp_securitysuite_recaptcha/backend/enabled'
        ]);
        $this->moveConfig([
            'msp_securitysuite_recaptcha/frontend/type' => 'msp_securitysuite_recaptcha/general/type'
        ]);

        // Move to new version configuration scheme
        $this->moveConfig([
            'msp_securitysuite_recaptcha/general' => 'recaptcha/general',
            'msp_securitysuite_recaptcha/backend' => 'recaptcha/backend',
            'msp_securitysuite_recaptcha/frontend' => 'recaptcha/frontend'
        ]);
    }

    /**
     * Move config from srcPath to dstPath
     *
     * @param array $paths
     */
    private function moveConfig(array $paths): void
    {
        $connection = $this->moduleDataSetup->getConnection();
        $configData = $this->moduleDataSetup->getTable('core_config_data');
        foreach ($paths as $srcPath => $dstPath) {
            $value = $this->scopeConfig->getValue($srcPath);
            if (is_array($value)) {
                foreach (array_keys($value) as $v) {
                    $this->moveConfig([$srcPath . '/' . $v => $dstPath . '/' . $v]);
                }
            } else {
                $connection->update($configData, ['path' => $dstPath], 'path=' . $connection->quote($srcPath));
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
