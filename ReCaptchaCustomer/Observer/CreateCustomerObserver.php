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
use Magento\ReCaptchaUi\Model\IsCaptchaEnabledInterface;
use Magento\ReCaptchaUi\Model\RequestHandlerInterface;

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
     * @var IsCaptchaEnabledInterface
     */
    private $isCaptchaEnabled;

    /**
     * @var RequestHandlerInterface
     */
    private $requestHandler;

    /**
     * @param UrlInterface $url
     * @param IsCaptchaEnabledInterface $isCaptchaEnabled
     * @param RequestHandlerInterface $requestHandler
     */
    public function __construct(
        UrlInterface $url,
        IsCaptchaEnabledInterface $isCaptchaEnabled,
        RequestHandlerInterface $requestHandler
    ) {
        $this->url = $url;
        $this->isCaptchaEnabled = $isCaptchaEnabled;
        $this->requestHandler = $requestHandler;
    }

    /**
     * @param Observer $observer
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Observer $observer): void
    {
        $key = 'customer_create';
        if ($this->isCaptchaEnabled->isCaptchaEnabledFor($key)) {
            /** @var Action $controller */
            $controller = $observer->getControllerAction();
            $request = $controller->getRequest();
            $response = $controller->getResponse();
            $redirectOnFailureUrl = $this->url->getUrl('*/*/create', ['_secure' => true]);

            $this->requestHandler->execute($key, $request, $response, $redirectOnFailureUrl);
        }
    }
}
