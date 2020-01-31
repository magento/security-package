<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptcha\Observer\Frontend;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Area;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\ReCaptcha\Model\CaptchaFailureHandling;
use Magento\ReCaptcha\Model\Config;
use Magento\ReCaptcha\Model\ValidateInterface;

/**
 * ReviewFormObserver
 */
class ReviewFormObserver implements ObserverInterface
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
     * @var RedirectInterface
     */
    private $redirect;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var CaptchaFailureHandling
     */
    private $captchaFailureHandling;

    /**
     * @param ValidateInterface $validate
     * @param RemoteAddress $remoteAddress
     * @param RedirectInterface $redirect
     * @param Config $config
     * @param CaptchaFailureHandling $captchaFailureHandling
     */
    public function __construct(
        ValidateInterface $validate,
        RemoteAddress $remoteAddress,
        RedirectInterface $redirect,
        Config $config,
        CaptchaFailureHandling $captchaFailureHandling
    ) {
        $this->validate = $validate;
        $this->remoteAddress = $remoteAddress;
        $this->redirect = $redirect;
        $this->config = $config;
        $this->captchaFailureHandling = $captchaFailureHandling;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        if ($this->config->isAreaEnabled(Area::AREA_FRONTEND) && $this->config->isEnabledFrontendReview()) {
            /** @var Action $controller */
            $controller = $observer->getControllerAction();
            $reCaptchaResponse = $controller->getRequest()->getParam(ValidateInterface::PARAM_RECAPTCHA_RESPONSE);

            $remoteIp = $this->remoteAddress->getRemoteAddress();

            if (!$this->validate->validate($reCaptchaResponse, $remoteIp)) {
                $url = $this->redirect->getRedirectUrl();
                $this->captchaFailureHandling->execute($controller->getResponse(), $url);
            }
        }
    }
}
