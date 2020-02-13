<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaUser\Observer;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;
use Magento\ReCaptchaApi\Api\CaptchaConfigInterface;
use Magento\ReCaptchaApi\Api\RequestHandlerInterface;

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
     * @var CaptchaConfigInterface
     */
    private $captchaConfig;

    /**
     * @param UrlInterface $url
     * @param RequestHandlerInterface $requestHandler
     * @param CaptchaConfigInterface $captchaConfig
     */
    public function __construct(
        UrlInterface $url,
        RequestHandlerInterface $requestHandler,
        CaptchaConfigInterface $captchaConfig
    ) {
        $this->url = $url;
        $this->requestHandler = $requestHandler;
        $this->captchaConfig = $captchaConfig;
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

        if ($this->captchaConfig->isCaptchaEnabledFor('user_forgot_password') && null !== $request->getParam('email')) {
            $response = $controller->getResponse();
            $redirectOnFailureUrl = $this->url->getUrl('*/*/forgotpassword', ['_secure' => true]);

            $this->requestHandler->execute($request, $response, $redirectOnFailureUrl);
        }
    }
}
