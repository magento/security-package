<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaNewsletter\Observer;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\ReCaptchaFrontendUi\Model\CaptchaRequestHandlerInterface;
use Magento\ReCaptchaNewsletter\Model\IsEnabledForNewsletterInterface;

/**
 * NewsletterObserver
 */
class NewsletterObserver implements ObserverInterface
{
    /**
     * @var RedirectInterface
     */
    private $redirect;

    /**
     * @var IsEnabledForNewsletterInterface
     */
    private $isEnabledForNewsletter;

    /**
     * @var CaptchaRequestHandlerInterface
     */
    private $captchaRequestHandler;

    /**
     * @param RedirectInterface $redirect
     * @param IsEnabledForNewsletterInterface $isEnabledForNewsletter
     * @param CaptchaRequestHandlerInterface $captchaRequestHandler
     */
    public function __construct(
        RedirectInterface $redirect,
        IsEnabledForNewsletterInterface $isEnabledForNewsletter,
        CaptchaRequestHandlerInterface $captchaRequestHandler
    ) {
        $this->redirect = $redirect;
        $this->isEnabledForNewsletter = $isEnabledForNewsletter;
        $this->captchaRequestHandler = $captchaRequestHandler;
    }

    /**
     * @param Observer $observer
     * @return void
     * @throws LocalizedException
     */
    public function execute(Observer $observer): void
    {
        if ($this->isEnabledForNewsletter->isEnabled()) {
            /** @var Action $controller */
            $controller = $observer->getControllerAction();
            $request = $controller->getRequest();
            $response = $controller->getResponse();
            $redirectOnFailureUrl = $this->redirect->getRefererUrl();

            $this->captchaRequestHandler->execute($request, $response, $redirectOnFailureUrl);
        }
    }
}
