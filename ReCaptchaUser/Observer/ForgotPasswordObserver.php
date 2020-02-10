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
use Magento\ReCaptcha\Model\ConfigInterface;
use Magento\ReCaptchaAdminUi\Model\AdminConfigInterface;
use Magento\ReCaptchaAdminUi\Model\CaptchaRequestHandlerInterface;

/**
 * ForgotPasswordObserver
 */
class ForgotPasswordObserver implements ObserverInterface
{
    /**
     * @var ConfigInterface
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
     * @var AdminConfigInterface
     */
    private $reCaptchaAdminConfig;

    /**
     * @param ConfigInterface $config
     * @param UrlInterface $url
     * @param CaptchaRequestHandlerInterface $captchaRequestHandler
     * @param AdminConfigInterface $reCaptchaAdminConfig
     */
    public function __construct(
        ConfigInterface $config,
        UrlInterface $url,
        CaptchaRequestHandlerInterface $captchaRequestHandler,
        AdminConfigInterface $reCaptchaAdminConfig
    ) {
        $this->config = $config;
        $this->url = $url;
        $this->captchaRequestHandler = $captchaRequestHandler;
        $this->reCaptchaAdminConfig = $reCaptchaAdminConfig;
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

        if ($this->reCaptchaAdminConfig->isBackendEnabled() && null !== $request->getParam('email')) {
            $response = $controller->getResponse();
            $redirectOnFailureUrl = $this->url->getUrl('*/*/forgotpassword', ['_secure' => true]);

            $this->captchaRequestHandler->execute($request, $response, $redirectOnFailureUrl);
        }
    }
}
