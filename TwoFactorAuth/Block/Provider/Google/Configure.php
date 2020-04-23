<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Block\Provider\Google;

use Magento\Backend\Block\Template;
use Magento\Backend\Model\Auth\Session;
use Magento\TwoFactorAuth\Model\Provider\Engine\Google;

/**
 * @api
 */
class Configure extends Template
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var Google
     */
    private $google;

    /**
     * @param Template\Context $context
     * @param Google $google
     * @param Session $session
     * @param array $data
     */
    public function __construct(Template\Context $context, Google $google, Session $session, array $data = [])
    {
        $this->session = $session;
        $this->google  = $google;

        parent::__construct($context, $data);
    }

    /**
     * @inheritdoc
     */
    public function getJsLayout()
    {
        $this->jsLayout['components']['tfa-configure']['postUrl'] =
            $this->getUrl('*/*/configurepost');

        $this->jsLayout['components']['tfa-configure']['qrCodeUrl'] =
            $this->getUrl('*/*/qr');

        $this->jsLayout['components']['tfa-configure']['successUrl'] =
            $this->getUrl($this->_urlBuilder->getStartupPageUrl());

        $this->jsLayout['components']['tfa-configure']['secretCode'] =
            $this->google->getSecretCode($this->session->getUser());

        return parent::getJsLayout();
    }
}
