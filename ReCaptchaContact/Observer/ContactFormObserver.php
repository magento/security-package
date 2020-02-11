<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaContact\Observer;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;
use Magento\ReCaptcha\Model\RequestHandlerInterface;
use Magento\ReCaptchaContact\Model\IsEnabledForContactInterface;

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
     * @var IsEnabledForContactInterface
     */
    private $isEnabledForContact;

    /**
     * @var RequestHandlerInterface
     */
    private $requestHandler;

    /**
     * @param UrlInterface $url
     * @param IsEnabledForContactInterface $isEnabledForContact
     * @param RequestHandlerInterface $requestHandler
     */
    public function __construct(
        UrlInterface $url,
        IsEnabledForContactInterface $isEnabledForContact,
        RequestHandlerInterface $requestHandler
    ) {
        $this->url = $url;
        $this->isEnabledForContact = $isEnabledForContact;
        $this->requestHandler = $requestHandler;
    }

    /**
     * @param Observer $observer
     * @return void
     * @throws LocalizedException
     */
    public function execute(Observer $observer): void
    {
        if ($this->isEnabledForContact->isEnabled()) {
            /** @var Action $controller */
            $controller = $observer->getControllerAction();
            $request = $controller->getRequest();
            $response = $controller->getResponse();
            $redirectOnFailureUrl = $this->url->getUrl('contact/index/index');

            $this->requestHandler->execute($request, $response, $redirectOnFailureUrl);
        }
    }
}
