<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model\Provider\Engine\U2fKey;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\TwoFactorAuth\Api\U2fKeyConfigReaderInterface;
use Magento\TwoFactorAuth\Model\Provider\Engine\U2fKey;

/**
 * Read the configuration for u2f provider
 */
class WebApiConfigReader implements U2fKeyConfigReaderInterface
{
    /**
     * @var ConfigReader
     */
    private $configReader;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param ConfigReader $configReader
     */
    public function __construct(ScopeConfigInterface $scopeConfig, ConfigReader $configReader)
    {
        $this->configReader = $configReader;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @inheritDoc
     */
    public function getDomain(): string
    {
        $configValue = $this->scopeConfig->getValue(U2fKey::XML_PATH_WEBAPI_DOMAIN);
        if ($configValue) {
            return $configValue;
        }

        return $this->configReader->getDomain();
    }
}
