<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaContact\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\ReCaptcha\Model\ConfigEnabledInterface;
use Magento\ReCaptcha\Model\ConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Read configuration from store config
 */
class ConfigEnabled implements ConfigEnabledInterface
{
    public const XML_PATH_ENABLED_FRONTEND_CONTACT = 'recaptcha/frontend/enabled_contact';

    /**
     * @var ConfigInterface
     */
    private $reCaptchaConfig;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param ConfigInterface $reCaptchaConfig
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ConfigInterface $reCaptchaConfig,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->reCaptchaConfig = $reCaptchaConfig;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Return true if enabled on frontend captcha for review
     * @return bool
     */
    public function isEnabled(): bool
    {
        if (!$this->reCaptchaConfig->isEnabledFrontend()) {
            return false;
        }

        return (bool)$this->scopeConfig->getValue(
            static::XML_PATH_ENABLED_FRONTEND_CONTACT,
            ScopeInterface::SCOPE_WEBSITE
        );
    }
}
