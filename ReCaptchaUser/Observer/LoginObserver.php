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
use Magento\ReCaptchaApi\Api\CaptchaConfigInterface;
use Magento\ReCaptchaApi\Api\CaptchaValidatorInterface;
use Magento\ReCaptchaApi\Api\Data\ValidationConfigInterface;
use Magento\ReCaptchaApi\Api\Data\ValidationConfigInterfaceFactory;

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
     * @var ValidationConfigInterfaceFactory
     */
    private $validationConfigFactory;

    /**
     * @param CaptchaValidatorInterface $captchaValidator
     * @param RemoteAddress $remoteAddress
     * @param CaptchaConfigInterface $captchaConfig
     * @param ValidationConfigInterfaceFactory $validationConfigFactory
     */
    public function __construct(
        CaptchaValidatorInterface $captchaValidator,
        RemoteAddress $remoteAddress,
        CaptchaConfigInterface $captchaConfig,
        ValidationConfigInterfaceFactory $validationConfigFactory
    ) {
        $this->captchaValidator = $captchaValidator;
        $this->remoteAddress = $remoteAddress;
        $this->captchaConfig = $captchaConfig;
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
        if ($this->captchaConfig->isCaptchaEnabledFor('user_login')) {
            /** @var Action $controller */
            $controller = $observer->getControllerAction();

            $reCaptchaResponse = $controller->getRequest()->getParam(
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
