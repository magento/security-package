<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaReview\Observer;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\ReCaptchaFrontendUi\Model\CaptchaRequestHandlerInterface;
use Magento\ReCaptchaReview\Model\IsEnabledForProductReviewInterface;

/**
 * ReviewFormObserver
 */
class ReviewFormObserver implements ObserverInterface
{
    /**
     * @var RedirectInterface
     */
    private $redirect;

    /**
     * @var IsEnabledForProductReviewInterface
     */
    private $isEnabledForProductReview;

    /**
     * @var CaptchaRequestHandlerInterface
     */
    private $captchaRequestHandler;

    /**
     * @param RedirectInterface $redirect
     * @param IsEnabledForProductReviewInterface $isEnabledForProductReview
     * @param CaptchaRequestHandlerInterface $captchaRequestHandler
     */
    public function __construct(
        RedirectInterface $redirect,
        IsEnabledForProductReviewInterface $isEnabledForProductReview,
        CaptchaRequestHandlerInterface $captchaRequestHandler
    ) {
        $this->redirect = $redirect;
        $this->isEnabledForProductReview = $isEnabledForProductReview;
        $this->captchaRequestHandler = $captchaRequestHandler;
    }

    /**
     * @param Observer $observer
     * @return void
     * @throws LocalizedException
     */
    public function execute(Observer $observer): void
    {
        if ($this->isEnabledForProductReview->isEnabled()) {
            /** @var Action $controller */
            $controller = $observer->getControllerAction();
            $request = $controller->getRequest();
            $response = $controller->getResponse();
            $redirectOnFailureUrl = $this->redirect->getRedirectUrl();

            $this->captchaRequestHandler->execute($request, $response, $redirectOnFailureUrl);
        }
    }
}
