<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaMigration\Setup\Patch\Data;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;

/**
 * Migrate config from frontend and backend scopes to recaptcha modules.
 */
class MigrateConfigToRecaptchaModules implements DataPatchInterface, PatchVersionInterface
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
     * @var WriterInterface
     */
    private $writer;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param ScopeConfigInterface $scopeConfig
     * @param WriterInterface $writer
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        ScopeConfigInterface $scopeConfig,
        WriterInterface $writer
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->scopeConfig = $scopeConfig;
        $this->writer = $writer;
    }

    /**
     * @inheritdoc
     */
    public function apply()
    {
        $scopes = ['frontend', 'backend'];
        foreach ($scopes as $scope) {
            $this->copyRecaptchaKeys($scope);
            $this->copyModuleSpecificRecords($scope);
            $this->copyEnabledRecaptcha($scope);
        }
    }

    /**
     * Copy 'enabled' reCAPTCHA.
     *
     * @param string $scope
     */
    private function copyEnabledRecaptcha(string $scope): void
    {
        $type = $this->getActiveRecaptchaType();
        if (!$type) {
            return;
        }

        $availableRecaptchaPreferences = $this->getAvailableRecaptchaPreferences();
        foreach ($availableRecaptchaPreferences[$scope] as $availableRecaptchaPreference) {
            $availableRecaptchaPreferencePath = "recaptcha_$scope/type_for/$availableRecaptchaPreference";
            $recaptchaPreferenceEnabled = $this->scopeConfig->getValue($availableRecaptchaPreferencePath);
            $recaptchaPreferenceEnabledLegacy = $this->scopeConfig->getValue("recaptcha/general/enabled_for_$availableRecaptchaPreference");
            if (null === $recaptchaPreferenceEnabled && null !== $recaptchaPreferenceEnabledLegacy) {
                $this->writer->save($availableRecaptchaPreferencePath, (int)$recaptchaPreferenceEnabledLegacy ? $type : null);
            }
        }
    }

    /**
     * Copy reCAPTCHA keys.
     *
     * @param string $scope
     */
    private function copyRecaptchaKeys(string $scope): void
    {
        $keys = ['public_key', 'private_key'];
        $type = $this->getActiveRecaptchaType();
        foreach ($keys as $key) {
            $this->copyRecord("recaptcha/general/$key", "recaptcha_$scope/type_$type/$key");
        }
    }

    /**
     * Copy module-specific records.
     *
     * @param string $scope
     */
    private function copyModuleSpecificRecords(string $scope): void
    {
        foreach ($this->getModuleSpecificRecords() as $module => $specificRecords) {
            foreach ($specificRecords as $specificRecord) {
                $this->copyRecord("recaptcha/general/$specificRecord", "recaptcha_$scope/type_$module/$specificRecord");
            }
        }

    }

    /**
     * Get module-specific records.
     *
     * @return array
     */
    private function getModuleSpecificRecords(): array
    {
        return [
            'recaptcha' => ['theme', 'size', 'lang'],
            'invisible' => ['position'],
            'recaptcha_v3' => ['score_threshold', 'position'],
        ];
    }

    /**
     * Get available recaptcha preferences.
     *
     * @return array
     */
    private function getAvailableRecaptchaPreferences(): array
    {
        return [
            'frontend' => [
                'customer_login',
                'customer_forgot_password',
                'customer_create',
                'contact',
                'product_review',
                'newsletter',
                'sendfriend',
            ],
            'backend' => [
                'user_login',
                'user_forgot_password',
            ],
        ];
    }

    /**
     * Get active reCAPTCHA type from config (recaptcha/scope/type).
     *
     * @return string|null
     */
    private function getActiveRecaptchaType(): ?string
    {
        return $this->scopeConfig->getValue('recaptcha/general/type');
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
        return [
            CopyConfigFromOldModule::class,
        ];
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
