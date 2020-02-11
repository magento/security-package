<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaUser\Observer;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\Plugin\AuthenticationException;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\ReCaptcha\Model\CaptchaConfigInterface;
use Magento\ReCaptcha\Model\ValidateInterface;
use Magento\ReCaptchaUser\Model\IsEnabledForUserLoginInterface;
use Magento\ReCaptcha\Model\ValidationConfigInterface;
use Magento\ReCaptcha\Model\ValidationConfigInterfaceFactory;

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
     * @var CaptchaConfigInterface
     */
    private $captchaConfig;

    /**
     * @var IsEnabledForUserLoginInterface
     */
    private $isEnabledForUserLogin;

    /**
     * @var ValidationConfigInterfaceFactory
     */
    private $validationConfigFactory;

    /**
     * @param ValidateInterface $validate
     * @param RemoteAddress $remoteAddress
     * @param CaptchaConfigInterface $captchaConfig
     * @param IsEnabledForUserLoginInterface $isEnabledForUserLogin
     * @param ValidationConfigInterfaceFactory $validationConfigFactory
     */
    public function __construct(
        ValidateInterface $validate,
        RemoteAddress $remoteAddress,
        CaptchaConfigInterface $captchaConfig,
        IsEnabledForUserLoginInterface $isEnabledForUserLogin,
        ValidationConfigInterfaceFactory $validationConfigFactory
    ) {
        $this->validate = $validate;
        $this->remoteAddress = $remoteAddress;
        $this->captchaConfig = $captchaConfig;
        $this->isEnabledForUserLogin = $isEnabledForUserLogin;
        $this->validationConfigFactory = $validationConfigFactory;
    }

    /**
     * @param Observer $observer
     * @return void
     * @throws AuthenticationException
     * @throws LocalizedException
     */
    public function execute(Observer $observer): void
    {
        if ($this->isEnabledForUserLogin->isEnabled()) {
            /** @var Action $controller */
            $controller = $observer->getControllerAction();

            $reCaptchaResponse = $controller->getRequest()->getParam(ValidateInterface::PARAM_RECAPTCHA_RESPONSE);
            /** @var ValidationConfigInterface $validationConfig */
            $validationConfig = $this->validationConfigFactory->create(
                [
                    'privateKey' => $this->captchaConfig->getPrivateKey(),
                    'captchaType' => $this->captchaConfig->getCaptchaType(),
                    'remoteIp' => $this->remoteAddress->getRemoteAddress(),
                    'scoreThreshold' => $this->captchaConfig->getScoreThreshold(),
                ]
            );

            if (false === $this->validate->validate($reCaptchaResponse, $validationConfig)) {
                throw new AuthenticationException($this->captchaConfig->getErrorMessage());
            }
        }
    }
}
