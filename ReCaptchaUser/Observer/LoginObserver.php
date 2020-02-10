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
use Magento\ReCaptcha\Model\ValidateInterface;
use Magento\ReCaptchaAdminUi\Model\AdminConfigInterface;
use Magento\ReCaptchaUser\Model\IsEnabledForUserLoginInterface;

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
     * @var AdminConfigInterface
     */
    private $reCaptchaAdminConfig;

    /**
     * @var IsEnabledForUserLoginInterface
     */
    private $isEnabledForUserLogin;

    /**
     * @param ValidateInterface $validate
     * @param RemoteAddress $remoteAddress
     * @param AdminConfigInterface $reCaptchaAdminConfig
     * @param IsEnabledForUserLoginInterface $isEnabledForUserLogin
     */
    public function __construct(
        ValidateInterface $validate,
        RemoteAddress $remoteAddress,
        AdminConfigInterface $reCaptchaAdminConfig,
        IsEnabledForUserLoginInterface $isEnabledForUserLogin
    ) {
        $this->validate = $validate;
        $this->remoteAddress = $remoteAddress;
        $this->reCaptchaAdminConfig = $reCaptchaAdminConfig;
        $this->isEnabledForUserLogin = $isEnabledForUserLogin;
    }

    /**
     * @param Observer $observer
     * @return void
     * @throws AuthenticationException
     * @throws LocalizedException
     */
    public function execute(Observer $observer): void
    {
        if ($this->reCaptchaAdminConfig->isEnabled()) {
            /** @var Action $controller */
            $controller = $observer->getControllerAction();

            $reCaptchaResponse = $controller->getRequest()->getParam(ValidateInterface::PARAM_RECAPTCHA_RESPONSE);
            $remoteIp = $this->remoteAddress->getRemoteAddress();
            $options['threshold'] = $this->reCaptchaAdminConfig->getMinScore();

            if (false === $this->validate->validate($reCaptchaResponse, $remoteIp, $options)) {
                throw new AuthenticationException($this->reCaptchaAdminConfig->getErrorMessage());
            }
        }
    }
}
