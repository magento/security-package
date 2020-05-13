<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Controller\Adminhtml\Tfa;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\TwoFactorAuth\Api\TfaInterface;
use Magento\TwoFactorAuth\Controller\Adminhtml\AbstractAction;
use Magento\TwoFactorAuth\Model\UserConfig\HtmlAreaTokenVerifier;
use Magento\Backend\Model\Auth\Session;

/**
 * Configure 2FA for the application.
 */
class Configure extends AbstractAction implements HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'Magento_TwoFactorAuth::config';

    /**
     * @var TfaInterface
     */
    private $tfa;

    /**
     * @var HtmlAreaTokenVerifier
     */
    private $tokenVerifier;

    /**
     * @var Session
     */
    private $session;

    /**
     * @param Context $context
     * @param TfaInterface $tfa
     * @param HtmlAreaTokenVerifier $tokenVerifier
     * @param Session $session
     */
    public function __construct(
        Context $context,
        TfaInterface $tfa,
        HtmlAreaTokenVerifier $tokenVerifier,
        Session $session
    ) {
        parent::__construct($context);
        $this->tfa = $tfa;
        $this->tokenVerifier = $tokenVerifier;
        $this->session = $session;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $user = $this->session->getUser();
        if (!$this->tfa->getUserProviders((int)$user->getId()) && !$this->tokenVerifier->isConfigTokenProvided()) {
            return $this->_redirect('tfa/tfa/requestconfig');
        }

        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }
}
