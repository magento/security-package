<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaNewsletter\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\ReCaptchaFrontendUi\Model\ConfigEnabledInterface;
use Magento\ReCaptchaFrontendUi\Model\FrontendConfigInterface as ReCaptchaFrontendUiConfig;
use Magento\Store\Model\ScopeInterface;

/**
 * @inheritdoc
 */
class IsEnabledForNewsletter implements IsEnabledForNewsletterInterface, ConfigEnabledInterface
{
    private const XML_PATH_ENABLED_FOR_NEWSLETTER = 'recaptcha/frontend/enabled_for_newsletter';

    /**
     * @var ReCaptchaFrontendUiConfig
     */
    private $reCaptchaFrontendConfig;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param ReCaptchaFrontendUiConfig $reCaptchaFrontendConfig
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ReCaptchaFrontendUiConfig $reCaptchaFrontendConfig,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->reCaptchaFrontendConfig = $reCaptchaFrontendConfig;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @inheritdoc
     */
    public function isEnabled(): bool
    {
        if (!$this->reCaptchaFrontendConfig->isFrontendEnabled()
            || !$this->reCaptchaFrontendConfig->isInvisibleRecaptcha()
        ) {
            return false;
        }

        return (bool)$this->scopeConfig->getValue(
            static::XML_PATH_ENABLED_FOR_NEWSLETTER,
            ScopeInterface::SCOPE_WEBSITE
        );
    }
}
