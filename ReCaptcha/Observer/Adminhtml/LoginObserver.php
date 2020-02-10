<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptcha\Observer\Adminhtml;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\Plugin\AuthenticationException;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\ReCaptcha\Model\ConfigInterface;
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
     * @var ConfigInterface
     */
    private $config;

    /**
     * @param ValidateInterface $validate
     * @param RemoteAddress $remoteAddress
     * @param ConfigInterface $config
     */
    public function __construct(
        ValidateInterface $validate,
        RemoteAddress $remoteAddress,
        ConfigInterface $config
    ) {
        $this->validate = $validate;
        $this->remoteAddress = $remoteAddress;
        $this->config = $config;
    }

    /**
     * @param Observer $observer
     * @return void
     * @throws AuthenticationException
     * @throws LocalizedException
     */
    public function execute(Observer $observer): void
    {
        if ($this->config->isEnabledBackend()) {
            /** @var Action $controller */
            $controller = $observer->getControllerAction();

            $reCaptchaResponse = $controller->getRequest()->getParam(ValidateInterface::PARAM_RECAPTCHA_RESPONSE);
            $remoteIp = $this->remoteAddress->getRemoteAddress();
            $options['threshold'] = $this->config->getMinBackendScore();

            if (false === $this->validate->validate($reCaptchaResponse, $remoteIp, $options)) {
                throw new AuthenticationException($this->config->getErrorDescription());
            }
        }
    }
}
