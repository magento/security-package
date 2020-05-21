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
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\Plugin\AuthenticationException;
use Magento\ReCaptchaUi\Model\CaptchaResponseResolverInterface;
use Magento\ReCaptchaUi\Model\IsCaptchaEnabledInterface;
use Magento\ReCaptchaUi\Model\ValidationConfigResolverInterface;
use Magento\ReCaptchaValidationApi\Api\ValidatorInterface;
use Psr\Log\LoggerInterface;

/**
 * LoginObserver
 */
class LoginObserver implements ObserverInterface
{
    /**
     * @var CaptchaResponseResolverInterface
     */
    private $captchaResponseResolver;

    /**
     * @var ValidationConfigResolverInterface
     */
    private $validationConfigResolver;

    /**
     * @var ValidatorInterface
     */
    private $captchaValidator;

    /**
     * @var IsCaptchaEnabledInterface
     */
    private $isCaptchaEnabled;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var string
     */
    private $loginActionName;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param CaptchaResponseResolverInterface $captchaResponseResolver
     * @param ValidationConfigResolverInterface $validationConfigResolver
     * @param ValidatorInterface $captchaValidator
     * @param IsCaptchaEnabledInterface $isCaptchaEnabled
     * @param RequestInterface $request
     * @param LoggerInterface $logger
     * @param string $loginActionName
     */
    public function __construct(
        CaptchaResponseResolverInterface $captchaResponseResolver,
        ValidationConfigResolverInterface $validationConfigResolver,
        ValidatorInterface $captchaValidator,
        IsCaptchaEnabledInterface $isCaptchaEnabled,
        RequestInterface $request,
        LoggerInterface $logger,
        string $loginActionName
    ) {
        $this->captchaResponseResolver = $captchaResponseResolver;
        $this->validationConfigResolver = $validationConfigResolver;
        $this->captchaValidator = $captchaValidator;
        $this->isCaptchaEnabled = $isCaptchaEnabled;
        $this->request = $request;
        $this->loginActionName = $loginActionName;
        $this->logger = $logger;
    }

    /**
     * @param Observer $observer
     * @return void
     * @throws AuthenticationException
     * @throws LocalizedException
     */
    public function execute(Observer $observer): void
    {
        $key = 'user_login';
        if ($this->isCaptchaEnabled->isCaptchaEnabledFor($key)
            && $this->request->getFullActionName() === $this->loginActionName
        ) {
            $validationConfig = $this->validationConfigResolver->get($key);
            try {
                $reCaptchaResponse = $this->captchaResponseResolver->resolve($this->request);
            } catch (InputException $e) {
                $this->logger->error($e);
                throw new AuthenticationException(__($validationConfig->getValidationFailureMessage()));
            }

            $validationResult = $this->captchaValidator->isValid($reCaptchaResponse, $validationConfig);
            if (false === $validationResult->isValid()) {
                throw new AuthenticationException(__($validationConfig->getValidationFailureMessage()));
            }
        }
    }
}
