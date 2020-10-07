<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Observer;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Backend\App\AbstractAction;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\UrlInterface;
use Magento\TwoFactorAuth\Controller\Adminhtml\Tfa\Configure;
use Magento\TwoFactorAuth\Controller\Adminhtml\Tfa\Index;
use Magento\TwoFactorAuth\Api\TfaInterface;
use Magento\TwoFactorAuth\Api\TfaSessionInterface;
use Magento\TwoFactorAuth\Api\UserConfigRequestManagerInterface;
use Magento\TwoFactorAuth\Controller\Adminhtml\Tfa\Requestconfig;
use Magento\TwoFactorAuth\Model\UserConfig\HtmlAreaTokenVerifier;

/**
 * Handle redirection to 2FA page if required
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
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
     * @var AuthorizationInterface
     */
    private $authorization;

    /**
     * @var UserContextInterface
     */
    private $userContext;

    /**
     * @param TfaInterface $tfa
     * @param TfaSessionInterface $tfaSession
     * @param UserConfigRequestManagerInterface $configRequestManager
     * @param HtmlAreaTokenVerifier $tokenManager
     * @param ActionFlag $actionFlag
     * @param UrlInterface $url
     * @param AuthorizationInterface $authorization
     * @param UserContextInterface $userContext
     */
    public function __construct(
        TfaInterface $tfa,
        TfaSessionInterface $tfaSession,
        UserConfigRequestManagerInterface $configRequestManager,
        HtmlAreaTokenVerifier $tokenManager,
        ActionFlag $actionFlag,
        UrlInterface $url,
        AuthorizationInterface $authorization,
        UserContextInterface $userContext
    ) {
        $this->tfa = $tfa;
        $this->tfaSession = $tfaSession;
        $this->configRequestManager = $configRequestManager;
        $this->tokenManager = $tokenManager;
        $this->actionFlag = $actionFlag;
        $this->url = $url;
        $this->authorization = $authorization;
        $this->userContext = $userContext;
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
        $fullActionName = $observer->getEvent()->getData('request')->getFullActionName();
        $userId = $this->userContext->getUserId();

        $this->tokenManager->readConfigToken();

        if (in_array($fullActionName, $this->tfa->getAllowedUrls(), true)) {
            //Actions that are used for 2FA must remain accessible.
            return;
        }

        if ($userId) {
            $configurationStillRequired = $this->configRequestManager->isConfigurationRequiredFor($userId);
            $toActivate = $this->tfa->getProvidersToActivate($userId);
            $toActivateCodes = [];
            foreach ($toActivate as $toActivateProvider) {
                $toActivateCodes[] = $toActivateProvider->getCode();
            }
            $accessGranted = $this->tfaSession->isGranted();

            if (!$accessGranted && $configurationStillRequired) {
                //User needs special link with a token to be allowed to configure 2FA
                if ($this->authorization->isAllowed(Requestconfig::ADMIN_RESOURCE)) {
                    $this->redirect('tfa/tfa/requestconfig');
                } else {
                    $this->redirect('tfa/tfa/accessdenied');
                }
            } else {
                if (!$accessGranted) {
                    if ($this->authorization->isAllowed(Index::ADMIN_RESOURCE)) {
                        $this->redirect('tfa/tfa/index');
                    } else {
                        $this->redirect('tfa/tfa/accessdenied');
                    }
                }
            }
        }
    }
}
