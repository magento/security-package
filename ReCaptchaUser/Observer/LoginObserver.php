<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaUser\Observer\Adminhtml;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\Plugin\AuthenticationException;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\ReCaptcha\Model\ValidateInterface;
use Magento\ReCaptchaAdminUi\Model\AdminConfigInterface;

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
     * @param ValidateInterface $validate
     * @param RemoteAddress $remoteAddress
     * @param AdminConfigInterface $reCaptchaAdminConfig
     */
    public function __construct(
        ValidateInterface $validate,
        RemoteAddress $remoteAddress,
        AdminConfigInterface $reCaptchaAdminConfig
    ) {
        $this->validate = $validate;
        $this->remoteAddress = $remoteAddress;
        $this->reCaptchaAdminConfig = $reCaptchaAdminConfig;
    }

    /**
     * @param Observer $observer
     * @return void
     * @throws AuthenticationException
     * @throws LocalizedException
     */
    public function execute(Observer $observer): void
    {
        if ($this->reCaptchaAdminConfig->isBackendEnabled()) {
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
