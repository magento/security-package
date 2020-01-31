<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Block\Provider\U2fKey;

use Magento\Backend\Block\Template;
use Magento\Backend\Model\Auth\Session;
use Magento\TwoFactorAuth\Model\Provider\Engine\U2fKey;

/**
 * @api
 */
class Auth extends Template
{
    /**
     * @var U2fKey
     */
    private $u2fKey;

    /**
     * @var Session
     */
    private $session;

    /**
     * @param Template\Context $context
     * @param Session $session
     * @param U2fKey $u2fKey
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Session $session,
        U2fKey $u2fKey,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->u2fKey = $u2fKey;
        $this->session = $session;
    }

    /**
     * @inheritdoc
     */
    public function getJsLayout()
    {
        $this->jsLayout['components']['tfa-auth']['postUrl'] =
            $this->getUrl('*/*/authpost');

        $this->jsLayout['components']['tfa-auth']['successUrl'] =
            $this->getUrl($this->_urlBuilder->getStartupPageUrl());

        $this->jsLayout['components']['tfa-auth']['touchImageUrl'] =
            $this->getViewFileUrl('Magento_TwoFactorAuth::images/u2f/touch.png');

        $this->jsLayout['components']['tfa-auth']['authenticateData'] =
            $this->u2fKey->getAuthenticateData($this->session->getUser());

        return parent::getJsLayout();
    }
}
