<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaCheckoutSalesRule\Observer;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\InputException;
use Magento\ReCaptchaUi\Model\IsCaptchaEnabledInterface;
use Magento\ReCaptchaUi\Model\RequestHandlerInterface;

/**
 * Add ReCaptcha support for Coupon Code
 */
class CouponCodeObserver implements ObserverInterface
{
    private const CAPTCHA_KEY = 'coupon_code';

    /**
     * @var RedirectInterface
     */
    private $redirect;

    /**
     * @var IsCaptchaEnabledInterface
     */
    private $isCaptchaEnabled;

    /**
     * @var RequestHandlerInterface
     */
    private $requestHandler;

    /**
     * @param RedirectInterface $redirect
     * @param IsCaptchaEnabledInterface $isCaptchaEnabled
     * @param RequestHandlerInterface $requestHandler
     */
    public function __construct(
        RedirectInterface $redirect,
        IsCaptchaEnabledInterface $isCaptchaEnabled,
        RequestHandlerInterface $requestHandler
    ) {
        $this->redirect = $redirect;
        $this->isCaptchaEnabled = $isCaptchaEnabled;
        $this->requestHandler = $requestHandler;
    }

    /**
     * @inheritdoc
     * @param Observer $observer
     * @return void
     * @throws InputException
     */
    public function execute(Observer $observer): void
    {
        /** @var Action $controller */
        $controller = $observer->getControllerAction();
        $request_param = $controller->getRequest()->getParams();
        if (!isset($request_param['remove']) && $this->isCaptchaEnabled->isCaptchaEnabledFor(self::CAPTCHA_KEY)) {
            $request = $controller->getRequest();
            $response = $controller->getResponse();
            $redirectOnFailureUrl = $this->redirect->getRefererUrl();
            $this->requestHandler->execute(self::CAPTCHA_KEY, $request, $response, $redirectOnFailureUrl);
        }
    }
}
