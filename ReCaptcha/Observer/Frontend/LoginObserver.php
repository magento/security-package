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
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\ReCaptcha\Model\BeforeAuthUrlProvider;
use Magento\ReCaptcha\Model\CaptchaFailureHandling;
use Magento\ReCaptcha\Model\Config;
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
     * @var BeforeAuthUrlProvider
     */
    private $beforeAuthUrlProvider;

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
     * @param BeforeAuthUrlProvider $beforeAuthUrlProvider
     * @param Config $config
     * @param CaptchaFailureHandling $captchaFailureHandling
     */
    public function __construct(
        ValidateInterface $validate,
        RemoteAddress $remoteAddress,
        BeforeAuthUrlProvider $beforeAuthUrlProvider,
        Config $config,
        CaptchaFailureHandling $captchaFailureHandling
    ) {
        $this->validate = $validate;
        $this->remoteAddress = $remoteAddress;
        $this->beforeAuthUrlProvider = $beforeAuthUrlProvider;
        $this->config = $config;
        $this->captchaFailureHandling = $captchaFailureHandling;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        if ($this->config->isAreaEnabled(Area::AREA_FRONTEND) && $this->config->isEnabledFrontendLogin()) {
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
