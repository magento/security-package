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
use Magento\ReCaptchaAdminUi\Model\CaptchaRequestHandlerInterface;
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
     * @var CaptchaRequestHandlerInterface
     */
    private $captchaRequestHandler;

    /**
     * @var IsEnabledForUserForgotPasswordInterface
     */
    private $isEnabledForUserForgotPassword;

    /**
     * @param UrlInterface $url
     * @param CaptchaRequestHandlerInterface $captchaRequestHandler
     * @param IsEnabledForUserForgotPasswordInterface $isEnabledForUserForgotPassword
     */
    public function __construct(
        UrlInterface $url,
        CaptchaRequestHandlerInterface $captchaRequestHandler,
        IsEnabledForUserForgotPasswordInterface $isEnabledForUserForgotPassword
    ) {
        $this->url = $url;
        $this->captchaRequestHandler = $captchaRequestHandler;
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

            $this->captchaRequestHandler->execute($request, $response, $redirectOnFailureUrl);
        }
    }
}
