<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaPaypal\Observer;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\ReCaptchaUi\Model\CaptchaResponseResolverInterface;
use Magento\ReCaptchaUi\Model\IsCaptchaEnabledInterface;
use Magento\ReCaptchaUi\Model\ValidationConfigResolverInterface;
use Magento\ReCaptchaValidationApi\Api\ValidatorInterface;
use Psr\Log\LoggerInterface;

/**
 * AjaxLoginObserver
 */
class PayPalObserver implements ObserverInterface
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
     * @var ActionFlag
     */
    private $actionFlag;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var IsCaptchaEnabledInterface
     */
    private $isCaptchaEnabled;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param CaptchaResponseResolverInterface $captchaResponseResolver
     * @param ValidationConfigResolverInterface $validationConfigResolver
     * @param ValidatorInterface $captchaValidator
     * @param ActionFlag $actionFlag
     * @param SerializerInterface $serializer
     * @param IsCaptchaEnabledInterface $isCaptchaEnabled
     * @param LoggerInterface $logger
     */
    public function __construct(
        CaptchaResponseResolverInterface $captchaResponseResolver,
        ValidationConfigResolverInterface $validationConfigResolver,
        ValidatorInterface $captchaValidator,
        ActionFlag $actionFlag,
        SerializerInterface $serializer,
        IsCaptchaEnabledInterface $isCaptchaEnabled,
        LoggerInterface $logger
    ) {
        $this->captchaResponseResolver = $captchaResponseResolver;
        $this->validationConfigResolver = $validationConfigResolver;
        $this->captchaValidator = $captchaValidator;
        $this->actionFlag = $actionFlag;
        $this->serializer = $serializer;
        $this->isCaptchaEnabled = $isCaptchaEnabled;
        $this->logger = $logger;
    }

    /**
     * Validates reCaptcha response.
     *
     * @param Observer $observer
     * @return void
     * @throws LocalizedException
     */
    public function execute(Observer $observer): void
    {
        $key = 'paypal_payflowpro';
        if ($this->isCaptchaEnabled->isCaptchaEnabledFor($key)) {
            /** @var Action $controller */
            $controller = $observer->getControllerAction();
            $request = $controller->getRequest();
            $response = $controller->getResponse();

            $validationConfig = $this->validationConfigResolver->get($key);

            try {
                $reCaptchaResponse = $this->captchaResponseResolver->resolve($request);
            } catch (InputException $e) {
                $this->logger->error($e);
                $this->processError($response, $validationConfig->getValidationFailureMessage());
                return;
            }

            $validationResult = $this->captchaValidator->isValid($reCaptchaResponse, $validationConfig);
            if (false === $validationResult->isValid()) {
                $this->processError($response, $validationConfig->getValidationFailureMessage());
            }
        }
    }

    /**
     * @param ResponseInterface $response
     * @param string $message
     * @return void
     */
    private function processError(ResponseInterface $response, string $message): void
    {
        $this->actionFlag->set('', Action::FLAG_NO_DISPATCH, true);

        $jsonPayload = $this->serializer->serialize([
            'success' => false,
            'errors' => true,
            'error_messages' => $message,
        ]);
        $response->representJson($jsonPayload);
    }
}
