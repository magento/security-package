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
use Magento\ReCaptchaUi\Model\IsCaptchaEnabledInterface;
use Magento\ReCaptchaUi\Model\RequestHandlerInterface;

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
     * @var IsCaptchaEnabledInterface
     */
    private $isCaptchaEnabled;

    /**
     * @param UrlInterface $url
     * @param RequestHandlerInterface $requestHandler
     * @param IsCaptchaEnabledInterface $isCaptchaEnabled
     */
    public function __construct(
        UrlInterface $url,
        RequestHandlerInterface $requestHandler,
        IsCaptchaEnabledInterface $isCaptchaEnabled
    ) {
        $this->url = $url;
        $this->requestHandler = $requestHandler;
        $this->isCaptchaEnabled = $isCaptchaEnabled;
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

        $key = 'user_forgot_password';
        if ($this->isCaptchaEnabled->isCaptchaEnabledFor($key) && null !== $request->getParam('email')) {
            $response = $controller->getResponse();
            $redirectOnFailureUrl = $this->url->getUrl('*/*/forgotpassword', ['_secure' => true]);

            $this->requestHandler->execute($key, $request, $response, $redirectOnFailureUrl);
        }
    }
}
