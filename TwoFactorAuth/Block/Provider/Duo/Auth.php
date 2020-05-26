<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Block\Provider\Duo;

use Magento\Backend\Block\Template;
use Magento\Backend\Model\Auth\Session;
use Magento\TwoFactorAuth\Model\Provider\Engine\DuoSecurity;

/**
 * @api
 */
class Auth extends Template
{
    /**
     * @var DuoSecurity
     */
    private $duoSecurity;

    /**
     * @var Session
     */
    private $session;

    /**
     * @param Template\Context $context
     * @param Session $session
     * @param DuoSecurity $duoSecurity
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Session $session,
        DuoSecurity $duoSecurity,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->duoSecurity = $duoSecurity;
        $this->session = $session;
    }

    /**
     * @inheritdoc
     */
    public function getJsLayout()
    {
        $this->jsLayout['components']['tfa-auth']['postUrl'] =
            $this->getUrl('*/*/authpost', ['form_key' => $this->getFormKey()]);

        $this->jsLayout['components']['tfa-auth']['signature'] =
            $this->duoSecurity->getRequestSignature($this->session->getUser());

        $this->jsLayout['components']['tfa-auth']['apiHost'] =
            $this->duoSecurity->getApiHostname();

        return parent::getJsLayout();
    }
}
