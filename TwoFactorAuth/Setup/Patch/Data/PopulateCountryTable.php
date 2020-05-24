<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Setup\Patch\Data;

use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Populate country codes table
 */
class PopulateCountryTable implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var File
     */
    private $file;

    /**
     * @var Reader
     */
    private $moduleReader;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param SerializerInterface $serializer
     * @param File $file
     * @param Reader $moduleReader
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        SerializerInterface $serializer,
        File $file,
        Reader $moduleReader
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->file = $file;
        $this->moduleReader = $moduleReader;
        $this->serializer = $serializer;
    }

    /**
     * Install the country table
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();
        $connection = $this->moduleDataSetup->getConnection();

        $tableName = $this->moduleDataSetup->getTable('tfa_country_codes');

        $countryCodesJsonFile =
            $this->moduleReader->getModuleDir(false, 'Magento_TwoFactorAuth') . DIRECTORY_SEPARATOR . 'Setup' .
            DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'country_codes.json';

        $countryCodesJson = $this->file->read($countryCodesJsonFile);

        $countryCodes = $this->serializer->unserialize(trim($countryCodesJson));

        // @codingStandardsIgnoreStart
        foreach ($countryCodes as $countryCode) {
            $connection->insertOnDuplicate($tableName, $countryCode, ['dial_code']);
        }
        // @codingStandardsIgnoreEnd

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
