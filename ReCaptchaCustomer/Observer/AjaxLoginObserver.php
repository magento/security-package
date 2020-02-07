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
use Magento\ReCaptcha\Model\ConfigEnabledInterface;
use Magento\ReCaptcha\Model\ValidateInterface;
use Magento\ReCaptchaFrontendUi\Model\ConfigInterface;

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
     * @var ConfigInterface
     */
    private $reCaptchaFrontendConfig;

    /**
     * @var ConfigEnabledInterface
     */
    private $config;

    /**
     * @param ValidateInterface $validate
     * @param RemoteAddress $remoteAddress
     * @param ActionFlag $actionFlag
     * @param SerializerInterface $serializer
     * @param ConfigInterface $reCaptchaFrontendConfig
     * @param ConfigEnabledInterface $config
     */
    public function __construct(
        ValidateInterface $validate,
        RemoteAddress $remoteAddress,
        ActionFlag $actionFlag,
        SerializerInterface $serializer,
        ConfigInterface $reCaptchaFrontendConfig,
        ConfigEnabledInterface $config
    ) {
        $this->validate = $validate;
        $this->remoteAddress = $remoteAddress;
        $this->actionFlag = $actionFlag;
        $this->serializer = $serializer;
        $this->reCaptchaFrontendConfig = $reCaptchaFrontendConfig;
        $this->config = $config;
    }

    /**
     * @param Observer $observer
     * @return void
     * @throws LocalizedException
     */
    public function execute(Observer $observer): void
    {
        if ($this->config->isEnabled()) {
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
                    'message' => $this->reCaptchaFrontendConfig->getErrorDescription(),
                ]);

                $controller->getResponse()->representJson($jsonPayload);
            }
        }
    }
}
