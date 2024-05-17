<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use OTPHP\TOTPInterface;

class UpdateLeeway implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(ModuleDataSetupInterface $moduleDataSetup)
    {
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * Fetch Totp default period
     *
     * @return int
     */
    public function getDefaultPeriod()
    {
        return TOTPInterface::DEFAULT_PERIOD;
    }

    /**
     * Apply the data patch
     */
    public function apply()
    {
        $setup = $this->moduleDataSetup->getConnection();
        $setup->startSetup();
        $select = $setup->select()
            ->from('core_config_data', ['path'])
            ->where('path = ?', 'twofactorauth/google/leeway');

        $existingValue = $setup->fetchOne($select);
        $period = $this->getDefaultPeriod();
        if ($existingValue && $existingValue >= $period) {
            $newWindowValue = $period - 1;
            $setup->update(
                'core_config_data',
                ['value' => $newWindowValue],
                'path = "twofactorauth/google/leeway"'
            );
        }
        $setup->endSetup();

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
