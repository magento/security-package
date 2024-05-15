<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Block\Provider\Authy;

use Magento\Backend\Block\Template;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * @api
 */
class Auth extends Template
{
    /**
     * Config path for the 2FA Attempts
     */
    private const XML_PATH_2FA_RETRY_ATTEMPTS = 'twofactorauth/general/twofactorauth_retry';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @inheritdoc
     */
    public function getJsLayout()
    {
        $this->jsLayout['components']['tfa-auth']['postUrl'] =
            $this->getUrl('*/*/authpost');

        $this->jsLayout['components']['tfa-auth']['tokenRequestUrl'] =
            $this->getUrl('*/*/token');

        $this->jsLayout['components']['tfa-auth']['oneTouchUrl'] =
            $this->getUrl('*/*/onetouch');

        $this->jsLayout['components']['tfa-auth']['verifyOneTouchUrl'] =
            $this->getUrl('*/*/verifyonetouch');

        $this->jsLayout['components']['tfa-auth']['successUrl'] =
            $this->getUrl($this->_urlBuilder->getStartupPageUrl());

        $this->jsLayout['components']['tfa-auth']['attempts'] =
            $this->scopeConfig->getValue(self::XML_PATH_2FA_RETRY_ATTEMPTS);

        return parent::getJsLayout();
    }
}
