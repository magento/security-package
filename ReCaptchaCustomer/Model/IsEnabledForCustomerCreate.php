<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaCustomer\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\ReCaptchaFrontendUi\Model\ConfigEnabledInterface;
use Magento\ReCaptchaFrontendUi\Model\FrontendConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * @inheritdoc
 */
class IsEnabledForCustomerCreate implements IsEnabledForCustomerCreateInterface, ConfigEnabledInterface
{
    private const XML_PATH_ENABLED_FRONTEND_CREATE = 'recaptcha/frontend/enabled_for_customer_create';

    /**
     * @var FrontendConfigInterface
     */
    private $reCaptchaFrontendConfig;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param FrontendConfigInterface $reCaptchaFrontendConfig
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        FrontendConfigInterface $reCaptchaFrontendConfig,
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
        if (!$this->reCaptchaFrontendConfig->isFrontendEnabled()) {
            return false;
        }

        return (bool)$this->scopeConfig->getValue(
            static::XML_PATH_ENABLED_FRONTEND_CREATE,
            ScopeInterface::SCOPE_WEBSITE
        );
    }
}
