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
class Configure extends Template
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
     * @param U2fKey $u2fKey
     * @param U2fSession $u2fSession
     * @param Session $session
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        U2fKey $u2fKey,
        U2fSession $u2fSession,
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
        $this->jsLayout['components']['tfa-configure']['postUrl'] =
            $this->getUrl('*/*/configurepost');

        $this->jsLayout['components']['tfa-configure']['successUrl'] =
            $this->getUrl($this->_urlBuilder->getStartupPageUrl());

        $this->jsLayout['components']['tfa-configure']['touchImageUrl'] =
            $this->getViewFileUrl('Magento_TwoFactorAuth::images/u2f/touch.png');

        $this->jsLayout['components']['tfa-configure']['registerData'] = $this->getRegisterData();

        return parent::getJsLayout();
    }

    /**
     * Get the data required to issue a WebAuthn request
     *
     * @return array
     */
    public function getRegisterData(): array
    {
        $registerData = $this->u2fKey->getRegisterData($this->session->getUser());
        $this->u2fSession->setU2fChallenge($registerData['publicKey']['challenge']);

        return $registerData;
    }
}
