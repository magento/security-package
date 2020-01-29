<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptcha\Observer;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\ReCaptcha\Model\IsCheckRequiredInterface;
use Magento\ReCaptcha\Model\Provider\FailureProviderInterface;
use Magento\ReCaptcha\Model\Provider\ResponseProviderInterface;
use Magento\ReCaptcha\Model\ValidateInterface;

/**
 * DI configurable generic observer for ReCaptcha validation
 */
class ReCaptchaObserver implements ObserverInterface
{
    /**
     * @var FailureProviderInterface
     */
    private $failureProvider;

    /**
     * @var ValidateInterface
     */
    private $validate;

    /**
     * @var RemoteAddress
     */
    private $remoteAddress;

    /**
     * @var ResponseProviderInterface
     */
    private $responseProvider;

    /**
     * @var IsCheckRequiredInterface
     */
    private $isCheckRequired;

    /**
     * @param ResponseProviderInterface $responseProvider
     * @param ValidateInterface $validate
     * @param FailureProviderInterface $failureProvider
     * @param RemoteAddress $remoteAddress
     * @param IsCheckRequiredInterface $isCheckRequired
     */
    public function __construct(
        ResponseProviderInterface $responseProvider,
        ValidateInterface $validate,
        FailureProviderInterface $failureProvider,
        RemoteAddress $remoteAddress,
        IsCheckRequiredInterface $isCheckRequired
    ) {
        $this->responseProvider = $responseProvider;
        $this->validate = $validate;
        $this->failureProvider = $failureProvider;
        $this->remoteAddress = $remoteAddress;
        $this->isCheckRequired = $isCheckRequired;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        if ($this->isCheckRequired->execute()) {
            $reCaptchaResponse = $this->responseProvider->execute();
            $remoteIp = $this->remoteAddress->getRemoteAddress();

            /** @var Action $controller */
            $controller = $observer->getControllerAction();

            if (!$this->validate->validate($reCaptchaResponse, $remoteIp)) {
                $this->failureProvider->execute($controller ? $controller->getResponse() : null);
            }
        }
    }
}
