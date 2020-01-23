<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Block\Provider\U2fKey;

use Magento\Backend\Block\Template;
use Magento\TwoFactorAuth\Model\Provider\Engine\U2fKey;

/**
 * u2key configuration block
 */
class Configure extends Template
{
    /**
     * @var U2fKey
     */
    private $u2fKey;

    /**
     * @param Template\Context $context
     * @param U2fKey $u2fKey
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        U2fKey $u2fKey,
        array $data = []
    ) {

        parent::__construct($context, $data);
        $this->u2fKey = $u2fKey;
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

        $this->jsLayout['components']['tfa-configure']['registerData'] =
            $this->u2fKey->getRegisterData();

        return parent::getJsLayout();
    }
}
