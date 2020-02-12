<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaUser\Observer;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\Plugin\AuthenticationException;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\ReCaptchaApi\Api\CaptchaConfigInterface;
use Magento\ReCaptchaApi\Api\CaptchaValidatorInterface;
use Magento\ReCaptchaApi\Api\Data\ValidationConfigInterface;
use Magento\ReCaptchaApi\Api\Data\ValidationConfigInterfaceFactory;
use Magento\ReCaptchaUser\Model\IsEnabledForUserLoginInterface;

/**
 * LoginObserver
 */
class LoginObserver implements ObserverInterface
{
    /**
     * @var CaptchaValidatorInterface
     */
    private $captchaValidator;

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
     * @var RequestInterface
     */
    private $request;

    /**
     * @param CaptchaValidatorInterface $captchaValidator
     * @param RemoteAddress $remoteAddress
     * @param CaptchaConfigInterface $captchaConfig
     * @param IsEnabledForUserLoginInterface $isEnabledForUserLogin
     * @param ValidationConfigInterfaceFactory $validationConfigFactory
     * @param RequestInterface $request
     */
    public function __construct(
        CaptchaValidatorInterface $captchaValidator,
        RemoteAddress $remoteAddress,
        CaptchaConfigInterface $captchaConfig,
        IsEnabledForUserLoginInterface $isEnabledForUserLogin,
        ValidationConfigInterfaceFactory $validationConfigFactory,
        RequestInterface $request
    ) {
        $this->captchaValidator = $captchaValidator;
        $this->remoteAddress = $remoteAddress;
        $this->captchaConfig = $captchaConfig;
        $this->isEnabledForUserLogin = $isEnabledForUserLogin;
        $this->validationConfigFactory = $validationConfigFactory;
        $this->request = $request;
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
            $reCaptchaResponse = $this->request->getParam(
                CaptchaValidatorInterface::PARAM_RECAPTCHA_RESPONSE
            );
            /** @var ValidationConfigInterface $validationConfig */
            $validationConfig = $this->validationConfigFactory->create(
                [
                    'privateKey' => $this->captchaConfig->getPrivateKey(),
                    'captchaType' => $this->captchaConfig->getCaptchaType(),
                    'remoteIp' => $this->remoteAddress->getRemoteAddress(),
                    'scoreThreshold' => $this->captchaConfig->getScoreThreshold(),
                ]
            );

            if (false === $this->captchaValidator->validate($reCaptchaResponse, $validationConfig)) {
                throw new AuthenticationException($this->captchaConfig->getErrorMessage());
            }
        }
    }
}
