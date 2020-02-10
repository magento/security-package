<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaCustomer\Observer;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\UrlInterface;
use Magento\ReCaptchaCustomer\Model\IsEnabledForCustomerCreateInterface;
use Magento\ReCaptchaFrontendUi\Model\CaptchaRequestHandlerInterface;

/**
 * CreateCustomerObserver
 */
class CreateCustomerObserver implements ObserverInterface
{
    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @var IsEnabledForCustomerCreateInterface
     */
    private $isEnabledForCustomerCreate;

    /**
     * @var CaptchaRequestHandlerInterface
     */
    private $captchaRequestHandler;

    /**
     * @param UrlInterface $url
     * @param IsEnabledForCustomerCreateInterface $config
     * @param CaptchaRequestHandlerInterface $isEnabledForCustomerCreate
     */
    public function __construct(
        UrlInterface $url,
        IsEnabledForCustomerCreateInterface $config,
        CaptchaRequestHandlerInterface $isEnabledForCustomerCreate
    ) {
        $this->url = $url;
        $this->isEnabledForCustomerCreate = $config;
        $this->captchaRequestHandler = $isEnabledForCustomerCreate;
    }

    /**
     * @param Observer $observer
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Observer $observer): void
    {
        if ($this->isEnabledForCustomerCreate->isEnabled()) {
            /** @var Action $controller */
            $controller = $observer->getControllerAction();
            $request = $controller->getRequest();
            $response = $controller->getResponse();
            $redirectOnFailureUrl = $this->url->getUrl('*/*/create', ['_secure' => true]);

            $this->captchaRequestHandler->execute($request, $response, $redirectOnFailureUrl);
        }
    }
}
