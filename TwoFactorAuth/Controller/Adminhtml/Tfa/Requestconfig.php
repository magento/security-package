<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Controller\Adminhtml\Tfa;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\AuthorizationException;
use Magento\TwoFactorAuth\Api\Exception\NotificationExceptionInterface;
use Magento\TwoFactorAuth\Api\TfaInterface;
use Magento\TwoFactorAuth\Api\UserConfigRequestManagerInterface;
use Magento\TwoFactorAuth\Controller\Adminhtml\AbstractAction;
use Magento\TwoFactorAuth\Model\UserConfig\HtmlAreaTokenVerifier;

/**
 * Request 2FA config from the user.
 */
class Requestconfig extends AbstractAction implements HttpGetActionInterface, HttpPostActionInterface
{
    /**
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Magento_TwoFactorAuth::tfa';

    private const TFA_EMAIL_SENT = 'tfa_email_sent';

    /**
     * @var UserConfigRequestManagerInterface
     */
    private $configRequestManager;

    /**
     * @var HtmlAreaTokenVerifier
     */
    private $tokenVerifier;

    /**
     * @var TfaInterface
     */
    private $tfa;

    /**
     * @var Session
     */
    private $session;

    /**
     * @param Context $context
     * @param UserConfigRequestManagerInterface $configRequestManager
     * @param HtmlAreaTokenVerifier $tokenVerifier
     * @param TfaInterface $tfa
     * @param Session $session
     */
    public function __construct(
        Context $context,
        UserConfigRequestManagerInterface $configRequestManager,
        HtmlAreaTokenVerifier $tokenVerifier,
        TfaInterface $tfa,
        Session $session
    ) {
        parent::__construct($context);
        $this->configRequestManager = $configRequestManager;
        $this->tokenVerifier = $tokenVerifier;
        $this->tfa = $tfa;
        $this->session = $session;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $user = $this->session->getUser();
        if (!$this->configRequestManager->isConfigurationRequiredFor((int)$user->getId())) {
            throw new AuthorizationException(__('2FA is already configured for the user.'));
        }
        if ($this->tokenVerifier->isConfigTokenProvided()) {
            if (!$this->tfa->getForcedProviders()) {
                return $this->_redirect('tfa/tfa/configure');
            } else {
                return $this->_redirect('tfa/tfa/index');
            }
        }

        try {
            if (!$this->session->getData(self::TFA_EMAIL_SENT)) {
                $this->configRequestManager->sendConfigRequestTo($user);
                $this->session->setData(self::TFA_EMAIL_SENT, true);
            }
        } catch (AuthorizationException $exception) {
            $this->messageManager->addErrorMessage(
                'Please ask an administrator with sufficient access to configure 2FA first'
            );
        } catch (NotificationExceptionInterface $exception) {
            $this->messageManager->addErrorMessage('Failed to send the message. Please contact the administrator');
        }

        return $this->resultFactory->create(ResultFactory::TYPE_PAGE);
    }
}
