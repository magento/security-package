<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptcha\Observer\Frontend;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Area;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\ReCaptcha\Model\BeforeAuthUrlProvider;
use Magento\ReCaptcha\Model\CaptchaRequestHandler;
use Magento\ReCaptcha\Model\Config;

/**
 * LoginObserver
 */
class LoginObserver implements ObserverInterface
{
    /**
     * @var BeforeAuthUrlProvider
     */
    private $beforeAuthUrlProvider;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var CaptchaRequestHandler
     */
    private $captchaRequestHandler;

    /**
     * @param BeforeAuthUrlProvider $beforeAuthUrlProvider
     * @param Config $config
     * @param CaptchaRequestHandler $captchaRequestHandler
     */
    public function __construct(
        BeforeAuthUrlProvider $beforeAuthUrlProvider,
        Config $config,
        CaptchaRequestHandler $captchaRequestHandler
    ) {
        $this->beforeAuthUrlProvider = $beforeAuthUrlProvider;
        $this->config = $config;
        $this->captchaRequestHandler = $captchaRequestHandler;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        if ($this->config->isAreaEnabled(Area::AREA_FRONTEND) && $this->config->isEnabledFrontendLogin()) {
            /** @var Action $controller */
            $controller = $observer->getControllerAction();
            $request = $controller->getRequest();
            $response = $controller->getResponse();
            $redirectOnFailureUrl = $this->beforeAuthUrlProvider->execute();

            $this->captchaRequestHandler->execute($request, $response, $redirectOnFailureUrl);
        }
    }
}
