<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaCustomer\Observer;

use Magento\Customer\Model\Url;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\ReCaptchaCustomer\Model\IsEnabledForCustomerLoginInterface;
use Magento\ReCaptchaFrontendUi\Model\CaptchaRequestHandlerInterface;

/**
 * LoginObserver
 */
class LoginObserver implements ObserverInterface
{
    /**
     * @var IsEnabledForCustomerLoginInterface
     */
    private $isEnabledForCustomerLogin;

    /**
     * @var CaptchaRequestHandlerInterface
     */
    private $captchaRequestHandler;

    /**
     * @var SessionManagerInterface
     */
    private $sessionManager;

    /**
     * @var Url
     */
    private $url;

    /**
     * @param IsEnabledForCustomerLoginInterface $isEnabledForCustomerLogin
     * @param CaptchaRequestHandlerInterface $captchaRequestHandler
     * @param SessionManagerInterface $sessionManager
     * @param Url $url
     */
    public function __construct(
        IsEnabledForCustomerLoginInterface $isEnabledForCustomerLogin,
        CaptchaRequestHandlerInterface $captchaRequestHandler,
        SessionManagerInterface $sessionManager,
        Url $url
    ) {
        $this->isEnabledForCustomerLogin = $isEnabledForCustomerLogin;
        $this->captchaRequestHandler = $captchaRequestHandler;
        $this->sessionManager = $sessionManager;
        $this->url = $url;
    }

    /**
     * @param Observer $observer
     * @return void
     * @throws LocalizedException
     */
    public function execute(Observer $observer): void
    {
        if ($this->isEnabledForCustomerLogin->isEnabled()) {
            /** @var Action $controller */
            $controller = $observer->getControllerAction();
            $request = $controller->getRequest();
            $response = $controller->getResponse();
            $redirectOnFailureUrl = $this->sessionManager->getBeforeAuthUrl() ?: $this->url->getLoginUrl();

            $this->captchaRequestHandler->execute($request, $response, $redirectOnFailureUrl);
        }
    }
}
