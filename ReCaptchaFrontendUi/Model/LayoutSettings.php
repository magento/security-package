<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaFrontendUi\Model;

use Magento\ReCaptcha\Model\CaptchaConfigInterface;

/**
 * Extension point of the layout configuration setting for reCaptcha
 *
 * @api
 */
class LayoutSettings
{
    /**
     * @var CaptchaConfigInterface
     */
    private $captchaConfig;

    /**
     * @var ConfigEnabledInterface[]
     */
    private $configEnabledProviders;

    /**
     * @param CaptchaConfigInterface $captchaConfig
     * @param ConfigEnabledInterface[] $configEnabledProviders
     */
    public function __construct(
        CaptchaConfigInterface $captchaConfig,
        array $configEnabledProviders
    ) {
        $this->captchaConfig = $captchaConfig;
        $this->configEnabledProviders = $configEnabledProviders;
    }

    /**
     * Return captcha config for frontend
     * @return array
     */
    public function getCaptchaSettings(): array
    {
        $settings = [
            'siteKey' => $this->captchaConfig->getPublicKey(),
            'size' => $this->captchaConfig->getSize(),
            'badge' => $this->captchaConfig->getPosition(),
            'theme' => $this->captchaConfig->getTheme(),
            'lang' => $this->captchaConfig->getLanguageCode(),
        ];
        foreach ($this->configEnabledProviders as $key => $configEnabledProvider) {
            $settings['enabled'][$key] = $configEnabledProvider->isEnabled();
        }
        return $settings;
    }
}
