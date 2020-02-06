<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptcha\Model;

use Magento\ReCaptcha\Model\ConfigEnabledInterface;

/**
 * Extension point of the layout configuration setting for reCaptcha
 *
 * @api
 */
class LayoutSettings
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var ConfigEnabledInterface[]
     */
    private $configEnabledProviders;

    /**
     * @param Config $config
     * @param ConfigEnabledInterface[] $configEnabledProviders
     */
    public function __construct(
        Config $config,
        array $configEnabledProviders
    ) {
        $this->config = $config;
        $this->configEnabledProviders = $configEnabledProviders;
    }

    /**
     * Return captcha config for frontend
     * @return array
     */
    public function getCaptchaSettings(): array
    {
        $settings = [
            'siteKey' => $this->config->getPublicKey(),
            'size' => $this->config->getFrontendSize(),
            'badge' => $this->config->getFrontendPosition(),
            'theme' => $this->config->getFrontendTheme(),
            'lang' => $this->config->getLanguageCode(),
        ];
        foreach ($this->configEnabledProviders as $key => $configEnabledProvider) {
            $settings['enabled'][$key] = $configEnabledProvider->isEnabled();
        }
        return $settings;
    }
}
