<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Observer;

use Magento\Backend\App\AbstractAction;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\UrlInterface;
use Magento\User\Model\User;
use Magento\TwoFactorAuth\Api\TfaInterface;
use Magento\TwoFactorAuth\Api\TfaSessionInterface;
use Magento\TwoFactorAuth\Api\UserConfigRequestManagerInterface;
use Magento\TwoFactorAuth\Model\UserConfig\HtmlAreaTokenVerifier;

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
     * @var TfaSessionInterface
     */
    private $tfaSession;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var UserConfigRequestManagerInterface
     */
    private $configRequestManager;

    /**
     * @var AbstractAction|null
     */
    private $action;

    /**
     * @var HtmlAreaTokenVerifier
     */
    private $tokenManager;

    /**
     * @var ActionFlag
     */
    private $actionFlag;

    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @param TfaInterface $tfa
     * @param TfaSessionInterface $tfaSession
     * @param Session $session
     * @param UserConfigRequestManagerInterface $configRequestManager
     * @param HtmlAreaTokenVerifier $tokenManager
     * @param ActionFlag $actionFlag
     * @param UrlInterface $url
     */
    public function __construct(
        TfaInterface $tfa,
        TfaSessionInterface $tfaSession,
        Session $session,
        UserConfigRequestManagerInterface $configRequestManager,
        HtmlAreaTokenVerifier $tokenManager,
        ActionFlag $actionFlag,
        UrlInterface $url
    ) {
        $this->tfa = $tfa;
        $this->tfaSession = $tfaSession;
        $this->session = $session;
        $this->configRequestManager = $configRequestManager;
        $this->tokenManager = $tokenManager;
        $this->actionFlag = $actionFlag;
        $this->url = $url;
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
     * Redirect user to given URL.
     *
     * @param string $url
     * @return void
     */
    private function redirect(string $url): void
    {
        $this->actionFlag->set('', Action::FLAG_NO_DISPATCH, true);
        $this->action->getResponse()->setRedirect($this->url->getUrl($url));
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        /** @var $controllerAction AbstractAction */
        $controllerAction = $observer->getEvent()->getData('controller_action');
        $this->action = $controllerAction;
        $fullActionName = $controllerAction->getRequest()->getFullActionName();
        $user = $this->getUser();

        $this->tokenManager->readConfigToken();

        if (in_array($fullActionName, $this->tfa->getAllowedUrls(), true)) {
            //Actions that are used for 2FA must remain accessible.
            return;
        }

        if ($user) {
            $configurationStillRequired = $this->configRequestManager->isConfigurationRequiredFor((int)$user->getId());
            $toActivate = $this->tfa->getProvidersToActivate((int)$user->getId());
            $toActivateCodes = [];
            foreach ($toActivate as $toActivateProvider) {
                $toActivateCodes[] = $toActivateProvider->getCode();
            }
            $currentlySkipped = $this->session->getData('tfa_skipped_config') ?? [];

            if ($configurationStillRequired && array_diff($toActivateCodes, array_keys($currentlySkipped))) {
                //User needs special link with a token to be allowed to configure 2FA
                $this->redirect('tfa/tfa/requestconfig');
            } else {
                //2FA required
                $accessGranted = $this->tfaSession->isGranted();
                if (!$accessGranted) {
                    $this->redirect('tfa/tfa/index');
                }
            }
        }
    }
}
