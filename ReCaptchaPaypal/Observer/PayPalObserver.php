<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaPaypal\Observer;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\ReCaptchaPaypal\Model\ReCaptchaSession;
use Magento\ReCaptchaUi\Model\CaptchaResponseResolverInterface;
use Magento\ReCaptchaUi\Model\ErrorMessageConfigInterface;
use Magento\ReCaptchaUi\Model\IsCaptchaEnabledInterface;
use Magento\ReCaptchaUi\Model\ValidationConfigResolverInterface;
use Magento\ReCaptchaValidationApi\Api\ValidatorInterface;
use Magento\ReCaptchaValidationApi\Model\ValidationErrorMessagesProvider;
use Psr\Log\LoggerInterface;

/**
 * reCaptcha support for PayPal Payflow Pro Integration.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
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
     * @var ErrorMessageConfigInterface
     */
    private $errorMessageConfig;

    /**
     * @var ValidationErrorMessagesProvider
     */
    private $validationErrorMessagesProvider;

    /**
     * @var ReCaptchaSession
     */
    private $reCaptchaSession;

    /**
     * @param CaptchaResponseResolverInterface $captchaResponseResolver
     * @param ValidationConfigResolverInterface $validationConfigResolver
     * @param ValidatorInterface $captchaValidator
     * @param ActionFlag $actionFlag
     * @param SerializerInterface $serializer
     * @param IsCaptchaEnabledInterface $isCaptchaEnabled
     * @param LoggerInterface $logger
     * @param ErrorMessageConfigInterface|null $errorMessageConfig
     * @param ValidationErrorMessagesProvider|null $validationErrorMessagesProvider
     * @param ReCaptchaSession|null $reCaptchaSession
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        CaptchaResponseResolverInterface $captchaResponseResolver,
        ValidationConfigResolverInterface $validationConfigResolver,
        ValidatorInterface $captchaValidator,
        ActionFlag $actionFlag,
        SerializerInterface $serializer,
        IsCaptchaEnabledInterface $isCaptchaEnabled,
        LoggerInterface $logger,
        ?ErrorMessageConfigInterface $errorMessageConfig = null,
        ?ValidationErrorMessagesProvider $validationErrorMessagesProvider = null,
        ?ReCaptchaSession $reCaptchaSession = null
    ) {
        $this->captchaResponseResolver = $captchaResponseResolver;
        $this->validationConfigResolver = $validationConfigResolver;
        $this->captchaValidator = $captchaValidator;
        $this->actionFlag = $actionFlag;
        $this->serializer = $serializer;
        $this->isCaptchaEnabled = $isCaptchaEnabled;
        $this->logger = $logger;
        $this->errorMessageConfig = $errorMessageConfig
            ?? ObjectManager::getInstance()->get(ErrorMessageConfigInterface::class);
        $this->validationErrorMessagesProvider = $validationErrorMessagesProvider
            ?? ObjectManager::getInstance()->get(ValidationErrorMessagesProvider::class);
        $this->reCaptchaSession = $reCaptchaSession
            ?? ObjectManager::getInstance()->get(ReCaptchaSession::class);
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
                $this->processError(
                    $response,
                    [],
                    $key
                );
                return;
            }

            $validationResult = $this->captchaValidator->isValid($reCaptchaResponse, $validationConfig);
            if (false === $validationResult->isValid()) {
                $this->processError(
                    $response,
                    $validationResult->getErrors(),
                    $key
                );
            } elseif ($this->isCaptchaEnabled->isCaptchaEnabledFor('place_order')) {
                // Extend reCaptcha verification to place order
                $this->reCaptchaSession->save();
            }
        }
    }

    /**
     * Process errors from reCAPTCHA response.
     *
     * @param ResponseInterface $response
     * @param array $errorMessages
     * @param string $sourceKey
     * @return void
     */
    private function processError(ResponseInterface $response, array $errorMessages, string $sourceKey): void
    {
        $validationErrorText = $this->errorMessageConfig->getValidationFailureMessage();
        $technicalErrorText = $this->errorMessageConfig->getTechnicalFailureMessage();

        $message = $errorMessages ? $validationErrorText : $technicalErrorText;

        foreach ($errorMessages as $errorMessageCode => $errorMessageText) {
            if (!$this->isValidationError($errorMessageCode)) {
                $message = $technicalErrorText;
                $this->logger->error(
                    __(
                        'reCAPTCHA \'%1\' form error: %2',
                        $sourceKey,
                        $errorMessageText
                    )
                );
            }
        }

        $this->actionFlag->set('', Action::FLAG_NO_DISPATCH, true);

        $jsonPayload = $this->serializer->serialize([
            'success' => false,
            'errors' => true,
            'error_messages' => $message,
        ]);
        $response->representJson($jsonPayload);
    }

    /**
     * Check if error code present in validation errors list.
     *
     * @param string $errorMessageCode
     * @return bool
     */
    private function isValidationError(string $errorMessageCode): bool
    {
        return $errorMessageCode !== $this->validationErrorMessagesProvider->getErrorMessage($errorMessageCode);
    }
}
