<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptcha\Observer\Adminhtml;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;
use Magento\ReCaptcha\Model\CaptchaRequestHandlerInterface;
use Magento\ReCaptcha\Model\Config;

/**
 * ForgotPasswordObserver
 */
class ForgotPasswordObserver implements ObserverInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @var CaptchaRequestHandlerInterface
     */
    private $captchaRequestHandler;

    /**
     * @param Config $config
     * @param UrlInterface $url
     * @param CaptchaRequestHandlerInterface $captchaRequestHandler
     */
    public function __construct(
        Config $config,
        UrlInterface $url,
        CaptchaRequestHandlerInterface $captchaRequestHandler
    ) {
        $this->config = $config;
        $this->url = $url;
        $this->captchaRequestHandler = $captchaRequestHandler;
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

        if ($this->config->isEnabledBackend() && null !== $request->getParam('email')) {
            $response = $controller->getResponse();
            $redirectOnFailureUrl = $this->url->getUrl('*/*/forgotpassword', ['_secure' => true]);

            $this->captchaRequestHandler->execute($request, $response, $redirectOnFailureUrl);
        }
    }
}
