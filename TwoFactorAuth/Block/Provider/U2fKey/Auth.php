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
use Magento\TwoFactorAuth\Model\Provider\Engine\U2fKey\Session as U2fSession;

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
     * @var U2fSession
     */
    private $u2fSession;

    /**
     * @var Session
     */
    private $session;

    /**
     * @param Template\Context $context
     * @param U2fSession $u2fSession
     * @param U2fKey $u2fKey
     * @param Session $session
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        U2fSession $u2fSession,
        U2fKey $u2fKey,
        Session $session,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->u2fKey = $u2fKey;
        $this->u2fSession = $u2fSession;
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

        $this->jsLayout['components']['tfa-auth']['authenticateData'] = $this->generateAuthenticateData();
        return parent::getJsLayout();
    }

    /**
     * Get the data needed to authenticate a webauthn request
     *
     * @return array
     */
    public function generateAuthenticateData(): array
    {
        $authenticateData = $this->u2fKey->getAuthenticateData($this->session->getUser());
        $this->u2fSession->setU2fChallenge($authenticateData['credentialRequestOptions']['challenge']);

        return $authenticateData;
    }
}
