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
use Magento\Framework\UrlInterface;
use Magento\ReCaptcha\Model\CaptchaRequestHandlerInterface;
use Magento\ReCaptcha\Model\Config;

/**
 * ContactFormObserver
 */
class ContactFormObserver implements ObserverInterface
{
    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var CaptchaRequestHandlerInterface
     */
    private $captchaRequestHandler;

    /**
     * @param UrlInterface $url
     * @param Config $config
     * @param CaptchaRequestHandlerInterface $captchaRequestHandler
     */
    public function __construct(
        UrlInterface $url,
        Config $config,
        CaptchaRequestHandlerInterface $captchaRequestHandler
    ) {
        $this->url = $url;
        $this->config = $config;
        $this->captchaRequestHandler = $captchaRequestHandler;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        if ($this->config->isAreaEnabled(Area::AREA_FRONTEND) && $this->config->isEnabledFrontendContact()) {
            /** @var Action $controller */
            $controller = $observer->getControllerAction();
            $request = $controller->getRequest();
            $response = $controller->getResponse();
            $redirectOnFailureUrl = $this->url->getUrl('contact/index/index');

            $this->captchaRequestHandler->execute($request, $response, $redirectOnFailureUrl);
        }
    }
}
