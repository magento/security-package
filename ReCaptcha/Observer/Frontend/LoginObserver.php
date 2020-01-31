<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptcha\Observer\Frontend;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\ReCaptcha\Model\BeforeAuthUrlProvider;
use Magento\ReCaptcha\Model\CaptchaFailureHandling;
use Magento\ReCaptcha\Model\IsCheckRequiredInterface;
use Magento\ReCaptcha\Model\ValidateInterface;

/**
 * LoginObserver
 */
class LoginObserver implements ObserverInterface
{
    /**
     * @var ValidateInterface
     */
    private $validate;

    /**
     * @var RemoteAddress
     */
    private $remoteAddress;

    /**
     * @var IsCheckRequiredInterface
     */
    private $isCheckRequired;

    /**
     * @var BeforeAuthUrlProvider
     */
    private $beforeAuthUrlProvider;

    /**
     * @var CaptchaFailureHandling
     */
    private $captchaFailureHandling;

    /**
     * @param ValidateInterface $validate
     * @param RemoteAddress $remoteAddress
     * @param IsCheckRequiredInterface $isCheckRequired
     * @param BeforeAuthUrlProvider $beforeAuthUrlProvider
     * @param CaptchaFailureHandling $captchaFailureHandling
     */
    public function __construct(
        ValidateInterface $validate,
        RemoteAddress $remoteAddress,
        IsCheckRequiredInterface $isCheckRequired,
        BeforeAuthUrlProvider $beforeAuthUrlProvider,
        CaptchaFailureHandling $captchaFailureHandling
    ) {
        $this->validate = $validate;
        $this->remoteAddress = $remoteAddress;
        $this->isCheckRequired = $isCheckRequired;
        $this->beforeAuthUrlProvider = $beforeAuthUrlProvider;
        $this->captchaFailureHandling = $captchaFailureHandling;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        if ($this->isCheckRequired->execute('frontend', 'recaptcha/frontend/enabled_login')) {
            /** @var Action $controller */
            $controller = $observer->getControllerAction();
            $reCaptchaResponse = $controller->getRequest()->getParam(ValidateInterface::PARAM_RECAPTCHA_RESPONSE);

            $remoteIp = $this->remoteAddress->getRemoteAddress();

            if (!$this->validate->validate($reCaptchaResponse, $remoteIp)) {
                $url = $this->beforeAuthUrlProvider->execute();
                $this->captchaFailureHandling->execute($controller->getResponse(), $url);
            }
        }
    }
}
