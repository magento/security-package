<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptcha\Model;

use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * @inheritDoc
 */
class IsCheckRequired implements IsCheckRequiredInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param Config $config
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Config $config
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->config = $config;
    }

    /**
     * Return true if area is configured to be active
     * @param string $area
     * @return bool
     */
    private function isAreaEnabled(string $area): bool
    {
        return
            (($area === Area::AREA_ADMINHTML) && $this->config->isEnabledBackend()) ||
            (($area === Area::AREA_FRONTEND) && $this->config->isEnabledFrontend());
    }

    /**
     * Return true if config is enabled
     * @param string $dependsOnConfig
     * @return bool
     */
    private function isConfigEnabled(string $dependsOnConfig): bool
    {
        return null === $dependsOnConfig || $this->scopeConfig->getValue($dependsOnConfig);
    }

    /**
     * @inheritdoc
     */
    public function execute(string $area, string $dependsOnConfig = null): bool
    {
        if (!in_array($area, [Area::AREA_FRONTEND, Area::AREA_ADMINHTML], true)) {
            throw new \InvalidArgumentException('Area parameter must be one of frontend or adminhtml');
        }

        return
            $this->isAreaEnabled($area) &&
            $this->isConfigEnabled($dependsOnConfig);
    }
}
