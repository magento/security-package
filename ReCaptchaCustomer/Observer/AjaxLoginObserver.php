<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaCustomer\Observer;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\ReCaptchaApi\Api\CaptchaConfigInterface;
use Magento\ReCaptchaApi\Api\CaptchaValidatorInterface;
use Magento\ReCaptchaApi\Api\Data\ValidationConfigInterface;
use Magento\ReCaptchaApi\Api\Data\ValidationConfigInterfaceFactory;

/**
 * AjaxLoginObserver
 */
class AjaxLoginObserver implements ObserverInterface
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
     * @var ActionFlag
     */
    private $actionFlag;

    /**
     * @var SerializerInterface
     */
    private $serializer;

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
     * @param ActionFlag $actionFlag
     * @param SerializerInterface $serializer
     * @param CaptchaConfigInterface $captchaConfig
     * @param ValidationConfigInterfaceFactory $validationConfigFactory
     */
    public function __construct(
        CaptchaValidatorInterface $captchaValidator,
        RemoteAddress $remoteAddress,
        ActionFlag $actionFlag,
        SerializerInterface $serializer,
        CaptchaConfigInterface $captchaConfig,
        ValidationConfigInterfaceFactory $validationConfigFactory
    ) {
        $this->captchaValidator = $captchaValidator;
        $this->remoteAddress = $remoteAddress;
        $this->actionFlag = $actionFlag;
        $this->serializer = $serializer;
        $this->captchaConfig = $captchaConfig;
        $this->validationConfigFactory = $validationConfigFactory;
    }

    /**
     * @param Observer $observer
     * @return void
     * @throws LocalizedException
     */
    public function execute(Observer $observer): void
    {
        if ($this->captchaConfig->isCaptchaEnabledFor('customer_login')) {
            /** @var Action $controller */
            $controller = $observer->getControllerAction();

            $reCaptchaResponse = '';
            if ($content = $controller->getRequest()->getContent()) {
                try {
                    $jsonParams = $this->serializer->unserialize($content);
                    if (isset($jsonParams[CaptchaValidatorInterface::PARAM_RECAPTCHA_RESPONSE])) {
                        $reCaptchaResponse = $jsonParams[CaptchaValidatorInterface::PARAM_RECAPTCHA_RESPONSE];
                    }
                } catch (\Exception $e) {
                    $this->handleCaptchaError($controller);
                    return;
                }
            }

            /** @var ValidationConfigInterface $validationConfig */
            $validationConfig = $this->validationConfigFactory->create(
                [
                    'privateKey' => $this->captchaConfig->getPrivateKey(),
                    'captchaType' => $this->captchaConfig->getCaptchaType(),
                    'remoteIp' => $this->remoteAddress->getRemoteAddress(),
                    'scoreThreshold' => $this->captchaConfig->getScoreThreshold(),
                ]
            );

            if (!$this->captchaValidator->isValid($reCaptchaResponse, $validationConfig)) {
                $this->handleCaptchaError($controller);
            }
        }
    }

    /**
     * Handle captcha error
     *
     * @param Action $controller
     */
    private function handleCaptchaError(Action $controller)
    {
        $this->actionFlag->set('', Action::FLAG_NO_DISPATCH, true);

        $jsonPayload = $this->serializer->serialize([
            'errors' => true,
            'message' => $this->captchaConfig->getErrorMessage(),
        ]);

        $controller->getResponse()->representJson($jsonPayload);
    }
}
