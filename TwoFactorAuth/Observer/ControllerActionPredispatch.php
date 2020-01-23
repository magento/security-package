<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Observer;

use Magento\Backend\App\AbstractAction;
use Magento\Backend\Model\Auth\Session;
use Magento\Backend\Model\UrlInterface;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\TwoFactorAuth\Api\TfaInterface;
use Magento\TwoFactorAuth\Api\TfaSessionInterface;
use Magento\TwoFactorAuth\Api\TrustedManagerInterface;
use Magento\User\Model\User;

/**
 * Handle redirection to 2FA page if required
 */
class ControllerActionPredispatch implements ObserverInterface
{
    /**
     * @var TfaInterface
     */
    private $tfa;

    /**
     * @var ActionFlag
     */
    private $actionFlag;

    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @var TfaSessionInterface
     */
    private $tfaSession;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var TrustedManagerInterface
     */
    private $trustedManager;

    /**
     * @param TfaInterface $tfa
     * @param ActionFlag $actionFlag
     * @param UrlInterface $url
     * @param Session $session
     * @param TfaSessionInterface $tfaSession
     * @param TrustedManagerInterface $trustedManager
     */
    public function __construct(
        TfaInterface $tfa,
        ActionFlag $actionFlag,
        UrlInterface $url,
        Session $session,
        TfaSessionInterface $tfaSession,
        TrustedManagerInterface $trustedManager
    ) {
        $this->tfa = $tfa;
        $this->actionFlag = $actionFlag;
        $this->url = $url;
        $this->tfaSession = $tfaSession;
        $this->session = $session;
        $this->trustedManager = $trustedManager;
    }

    /**
     * Get current user
     * @return User|null
     */
    private function getUser(): ?User
    {
        return $this->session->getUser();
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        if (!$this->tfa->isEnabled()) {
            return;
        }

        /** @var $controllerAction AbstractAction */
        $controllerAction = $observer->getEvent()->getControllerAction();
        $fullActionName = $controllerAction->getRequest()->getFullActionName();

        if (in_array($fullActionName, $this->tfa->getAllowedUrls(), true)) {
            return;
        }

        $user = $this->getUser();
        if ($user && !empty($this->tfa->getUserProviders((int) $user->getId()))) {
            $accessGranted = ($this->tfaSession->isGranted() || $this->trustedManager->isTrustedDevice()) &&
                empty($this->tfa->getProvidersToActivate((int) $user->getId()));

            if (!$accessGranted) {
                $this->actionFlag->set('', Action::FLAG_NO_DISPATCH, true);
                $url = $this->url->getUrl('tfa/tfa/index');
                $controllerAction->getResponse()->setRedirect($url);
            }
        }
    }
}
