<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaNewsletter\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\ReCaptcha\Model\CaptchaConfigInterface;
use Magento\ReCaptchaFrontendUi\Model\ConfigEnabledInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * @inheritdoc
 */
class IsEnabledForNewsletter implements IsEnabledForNewsletterInterface, ConfigEnabledInterface
{
    private const XML_PATH_ENABLED_FOR_NEWSLETTER = 'recaptcha/frontend/enabled_for_newsletter';

    /**
     * @var CaptchaConfigInterface
     */
    private $captchaConfig;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param CaptchaConfigInterface $captchaConfig
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        CaptchaConfigInterface $captchaConfig,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->captchaConfig = $captchaConfig;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @inheritdoc
     */
    public function isEnabled(): bool
    {
        if (!$this->captchaConfig->areKeysConfigured()
            || !$this->captchaConfig->isInvisibleRecaptcha()
        ) {
            return false;
        }

        return (bool)$this->scopeConfig->getValue(
            static::XML_PATH_ENABLED_FOR_NEWSLETTER,
            ScopeInterface::SCOPE_WEBSITE
        );
    }
}
