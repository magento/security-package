<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptcha\Model;

/**
 * Return the layout configuration setting for reCaptcha
 */
class LayoutSettings
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * Return captcha config for frontend
     * @return array
     */
    public function getCaptchaSettings(): array
    {
        return [
            'siteKey' => $this->config->getPublicKey(),
            'size' => $this->config->getFrontendSize(),
            'badge' => $this->config->getFrontendPosition(),
            'theme' => $this->config->getFrontendTheme(),
            'lang' => $this->config->getLanguageCode(),
            'enabled' => [
                'login' => $this->config->isEnabledFrontendLogin(),
                'create' => $this->config->isEnabledFrontendCreate(),
                'forgot' => $this->config->isEnabledFrontendForgot(),
                'contact' => $this->config->isEnabledFrontendContact(),
                'review' => $this->config->isEnabledFrontendReview(),
                'newsletter' => $this->config->isEnabledFrontendNewsletter(),
                'sendfriend' => $this->config->isEnabledFrontendSendFriend(),
            ]
        ];
    }
}
