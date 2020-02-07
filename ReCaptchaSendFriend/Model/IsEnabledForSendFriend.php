<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaSendFriend\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\ReCaptcha\Model\ConfigEnabledInterface;
use Magento\ReCaptchaFrontendUi\Model\ConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * @inheritdoc
 */
class IsEnabledForSendFriend implements IsEnabledForSendFriendInterface, ConfigEnabledInterface
{
    private const XML_PATH_ENABLED_FOR_SENDFRIEND = 'recaptcha/frontend/enabled_for_sendfriend';

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
     * @inheritdoc
     */
    public function isEnabled(): bool
    {
        if (!$this->reCaptchaConfig->isFrontendEnabled()) {
            return false;
        }

        return (bool)$this->scopeConfig->getValue(
            static::XML_PATH_ENABLED_FOR_SENDFRIEND,
            ScopeInterface::SCOPE_WEBSITE
        );
    }
}
