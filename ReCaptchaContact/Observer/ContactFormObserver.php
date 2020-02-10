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
use Magento\ReCaptchaContact\Model\IsEnabledForContactInterface;
use Magento\ReCaptchaFrontendUi\Model\CaptchaRequestHandlerInterface;

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
     * @var CaptchaRequestHandlerInterface
     */
    private $captchaRequestHandler;

    /**
     * @param UrlInterface $url
     * @param IsEnabledForContactInterface $isEnabledForContact
     * @param CaptchaRequestHandlerInterface $captchaRequestHandler
     */
    public function __construct(
        UrlInterface $url,
        IsEnabledForContactInterface $isEnabledForContact,
        CaptchaRequestHandlerInterface $captchaRequestHandler
    ) {
        $this->url = $url;
        $this->isEnabledForContact = $isEnabledForContact;
        $this->captchaRequestHandler = $captchaRequestHandler;
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

            $this->captchaRequestHandler->execute($request, $response, $redirectOnFailureUrl);
        }
    }
}
