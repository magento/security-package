<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaCustomer\Observer;

use Magento\Customer\Model\Url;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Area;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\ReCaptcha\Model\CaptchaRequestHandlerInterface;
use Magento\ReCaptcha\Model\ConfigEnabledInterface;

/**
 * LoginObserver
 */
class LoginObserver implements ObserverInterface
{
    /**
     * @var ConfigEnabledInterface
     */
    private $config;

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
     * @param ConfigEnabledInterface $config
     * @param CaptchaRequestHandlerInterface $captchaRequestHandler
     * @param SessionManagerInterface $sessionManager
     * @param Url $url
     */
    public function __construct(
        ConfigEnabledInterface $config,
        CaptchaRequestHandlerInterface $captchaRequestHandler,
        SessionManagerInterface $sessionManager,
        Url $url
    ) {
        $this->config = $config;
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
        if ($this->config->isEnabled()) {
            /** @var Action $controller */
            $controller = $observer->getControllerAction();
            $request = $controller->getRequest();
            $response = $controller->getResponse();
            $redirectOnFailureUrl = $this->sessionManager->getBeforeAuthUrl() ?: $this->url->getLoginUrl();

            $this->captchaRequestHandler->execute(Area::AREA_FRONTEND, $request, $response, $redirectOnFailureUrl);
        }
    }
}
