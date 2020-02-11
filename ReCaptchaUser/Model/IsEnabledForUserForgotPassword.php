<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaUser\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\ReCaptchaApi\Api\CaptchaConfigInterface;

/**
 * @inheritdoc
 */
class IsEnabledForUserForgotPassword implements IsEnabledForUserForgotPasswordInterface
{
    private const XML_PATH_ENABLED_FOR_USER_FORGOT_PASSWORD = 'recaptcha/backend/enabled_for_user_forgot_password';

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
        if (!$this->captchaConfig->areKeysConfigured()) {
            return false;
        }

        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_ENABLED_FOR_USER_FORGOT_PASSWORD
        );
    }
}
