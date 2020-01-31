<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptcha\Observer\Adminhtml;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Area;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\Plugin\AuthenticationException;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
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
     * @var Config
     */
    private $config;

    /**
     * @param ValidateInterface $validate
     * @param RemoteAddress $remoteAddress
     * @param Config $config
     */
    public function __construct(
        ValidateInterface $validate,
        RemoteAddress $remoteAddress,
        Config $config
    ) {
        $this->validate = $validate;
        $this->remoteAddress = $remoteAddress;
        $this->config = $config;
    }

    /**
     * @param Observer $observer
     * @return void
     * @throws AuthenticationException
     */
    public function execute(Observer $observer): void
    {
        if ($this->config->isAreaEnabled(Area::AREA_ADMINHTML)) {
            /** @var Action $controller */
            $controller = $observer->getControllerAction();
            $reCaptchaResponse = $controller->getRequest()->getParam(ValidateInterface::PARAM_RECAPTCHA_RESPONSE);

            $remoteIp = $this->remoteAddress->getRemoteAddress();

            if (!$this->validate->validate($reCaptchaResponse, $remoteIp)) {
                throw new AuthenticationException($this->config->getErrorDescription());
            }
        }
    }
}
