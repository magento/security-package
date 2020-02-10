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
use Magento\ReCaptcha\Model\ValidateInterface;
use Magento\ReCaptchaCustomer\Model\IsEnabledForCustomerLoginInterface;
use Magento\ReCaptchaFrontendUi\Model\FrontendConfigInterface;

/**
 * AjaxLoginObserver
 */
class AjaxLoginObserver implements ObserverInterface
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
     * @var ActionFlag
     */
    private $actionFlag;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var FrontendConfigInterface
     */
    private $reCaptchaFrontendConfig;

    /**
     * @var IsEnabledForCustomerLoginInterface
     */
    private $isEnabledForCustomerLogin;

    /**
     * @param ValidateInterface $validate
     * @param RemoteAddress $remoteAddress
     * @param ActionFlag $actionFlag
     * @param SerializerInterface $serializer
     * @param FrontendConfigInterface $reCaptchaFrontendConfig
     * @param IsEnabledForCustomerLoginInterface $isEnabledForCustomerLogin
     */
    public function __construct(
        ValidateInterface $validate,
        RemoteAddress $remoteAddress,
        ActionFlag $actionFlag,
        SerializerInterface $serializer,
        FrontendConfigInterface $reCaptchaFrontendConfig,
        IsEnabledForCustomerLoginInterface $isEnabledForCustomerLogin
    ) {
        $this->validate = $validate;
        $this->remoteAddress = $remoteAddress;
        $this->actionFlag = $actionFlag;
        $this->serializer = $serializer;
        $this->reCaptchaFrontendConfig = $reCaptchaFrontendConfig;
        $this->isEnabledForCustomerLogin = $isEnabledForCustomerLogin;
    }

    /**
     * @param Observer $observer
     * @return void
     * @throws LocalizedException
     */
    public function execute(Observer $observer): void
    {
        if ($this->isEnabledForCustomerLogin->isEnabled()) {
            /** @var Action $controller */
            $controller = $observer->getControllerAction();

            $reCaptchaResponse = '';
            if ($content = $controller->getRequest()->getContent()) {
                try {
                    $jsonParams = $this->serializer->unserialize($content);
                    if (isset($jsonParams[ValidateInterface::PARAM_RECAPTCHA_RESPONSE])) {
                        $reCaptchaResponse = $jsonParams[ValidateInterface::PARAM_RECAPTCHA_RESPONSE];
                    }
                } catch (\Exception $e) {
                    $reCaptchaResponse = '';
                }
            }

            $remoteIp = $this->remoteAddress->getRemoteAddress();
            $options['threshold'] = $this->reCaptchaFrontendConfig->getMinScore();

            if (!$this->validate->validate($reCaptchaResponse, $remoteIp, $options)) {
                $this->actionFlag->set('', Action::FLAG_NO_DISPATCH, true);

                $jsonPayload = $this->serializer->serialize([
                    'errors' => true,
                    'message' => $this->reCaptchaFrontendConfig->getErrorMessage(),
                ]);

                $controller->getResponse()->representJson($jsonPayload);
            }
        }
    }
}
