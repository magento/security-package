<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaUser\Observer\Adminhtml;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;
use Magento\ReCaptcha\Model\RequestHandlerInterface;
use Magento\ReCaptchaUser\Model\IsEnabledForUserForgotPasswordInterface;

/**
 * ForgotPasswordObserver
 */
class ForgotPasswordObserver implements ObserverInterface
{
    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @var RequestHandlerInterface
     */
    private $requestHandler;

    /**
     * @var IsEnabledForUserForgotPasswordInterface
     */
    private $isEnabledForUserForgotPassword;

    /**
     * @param UrlInterface $url
     * @param RequestHandlerInterface $requestHandler
     * @param IsEnabledForUserForgotPasswordInterface $isEnabledForUserForgotPassword
     */
    public function __construct(
        UrlInterface $url,
        RequestHandlerInterface $requestHandler,
        IsEnabledForUserForgotPasswordInterface $isEnabledForUserForgotPassword
    ) {
        $this->url = $url;
        $this->requestHandler = $requestHandler;
        $this->isEnabledForUserForgotPassword = $isEnabledForUserForgotPassword;
    }

    /**
     * @param Observer $observer
     * @return void
     * @throws LocalizedException
     */
    public function execute(Observer $observer): void
    {
        /** @var Action $controller */
        $controller = $observer->getControllerAction();
        $request = $controller->getRequest();

        if ($this->isEnabledForUserForgotPassword->isEnabled() && null !== $request->getParam('email')) {
            $response = $controller->getResponse();
            $redirectOnFailureUrl = $this->url->getUrl('*/*/forgotpassword', ['_secure' => true]);

            $this->requestHandler->execute($request, $response, $redirectOnFailureUrl);
        }
    }
}
