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

class UpdateOtpWindow implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    public function __construct(ModuleDataSetupInterface $moduleDataSetup)
    {
        $this->moduleDataSetup = $moduleDataSetup;
    }

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
            ->where('path = ?', 'twofactorauth/google/otp_window');

        $existingValue = $setup->fetchOne($select);
        $period = $this->getDefaultPeriod();
        if ($existingValue && $existingValue >= $period) {
            $newWindowValue = $period - 1;
        }

        if ($existingValue) {
            $setup->update(
                'core_config_data',
                ['value' => $newWindowValue],
                'path = "twofactorauth/google/otp_window"'
            );
        }

        $setup->endSetup();
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
