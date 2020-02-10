<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaUser\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\ReCaptchaAdminUi\Model\AdminConfigInterface;

/**
 * @inheritdoc
 */
class IsEnabledForUserLogin implements IsEnabledForUserLoginInterface
{
    private const XML_PATH_ENABLED_FOR_USER_LOGIN = 'recaptcha/backend/enabled_for_user_login';

    /**
     * @var AdminConfigInterface
     */
    private $reCaptchaAdminConfig;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param AdminConfigInterface $reCaptchaAdminConfig
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        AdminConfigInterface $reCaptchaAdminConfig,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->reCaptchaAdminConfig = $reCaptchaAdminConfig;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @inheritdoc
     */
    public function isEnabled(): bool
    {
        if (!$this->reCaptchaAdminConfig->getPrivateKey() || !$this->reCaptchaAdminConfig->getPublicKey()) {
            return false;
        }

        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_ENABLED_FOR_USER_LOGIN
        );
    }
}
