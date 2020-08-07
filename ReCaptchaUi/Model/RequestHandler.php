<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaUi\Model;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\HttpInterface as HttpResponseInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\ReCaptchaValidationApi\Api\ValidatorInterface;
use Magento\ReCaptchaValidationApi\Model\ValidationErrorMessagesProvider;
use Psr\Log\LoggerInterface;

/**
 * @inheritdoc
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class RequestHandler implements RequestHandlerInterface
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
     * @var MessageManagerInterface
     */
    private $messageManager;

    /**
     * @var ActionFlag
     */
    private $actionFlag;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ErrorMessageConfigInterface|null
     */
    private $errorMessageConfig;

    /**
     * @var ValidationErrorMessagesProvider|null
     */
    private $validationErrorMessagesProvider;

    /**
     * @param CaptchaResponseResolverInterface $captchaResponseResolver
     * @param ValidationConfigResolverInterface $validationConfigResolver
     * @param ValidatorInterface $captchaValidator
     * @param MessageManagerInterface $messageManager
     * @param ActionFlag $actionFlag
     * @param LoggerInterface $logger
     * @param ErrorMessageConfigInterface|null $errorMessageConfig
     * @param ValidationErrorMessagesProvider|null $validationErrorMessagesProvider
     */
    public function __construct(
        CaptchaResponseResolverInterface $captchaResponseResolver,
        ValidationConfigResolverInterface $validationConfigResolver,
        ValidatorInterface $captchaValidator,
        MessageManagerInterface $messageManager,
        ActionFlag $actionFlag,
        LoggerInterface $logger,
        ?ErrorMessageConfigInterface $errorMessageConfig = null,
        ?ValidationErrorMessagesProvider $validationErrorMessagesProvider = null
    ) {
        $this->captchaResponseResolver = $captchaResponseResolver;
        $this->validationConfigResolver = $validationConfigResolver;
        $this->captchaValidator = $captchaValidator;
        $this->messageManager = $messageManager;
        $this->actionFlag = $actionFlag;
        $this->logger = $logger;
        $this->errorMessageConfig = $errorMessageConfig
            ?? ObjectManager::getInstance()->get(ErrorMessageConfigInterface::class);
        $this->validationErrorMessagesProvider = $validationErrorMessagesProvider
            ?? ObjectManager::getInstance()->get(ValidationErrorMessagesProvider::class);
    }

    /**
     * @inheritdoc
     */
    public function execute(
        string $key,
        RequestInterface $request,
        HttpResponseInterface $response,
        string $redirectOnFailureUrl
    ): void {
        $validationConfig = $this->validationConfigResolver->get($key);

        try {
            $reCaptchaResponse = $this->captchaResponseResolver->resolve($request);
        } catch (InputException $e) {
            $this->logger->error($e);
            $this->processError($response, [], $redirectOnFailureUrl, $key);
            return;
        }

        $validationResult = $this->captchaValidator->isValid($reCaptchaResponse, $validationConfig);
        if (false === $validationResult->isValid()) {
            $this->processError($response, $validationResult->getErrors(), $redirectOnFailureUrl, $key);
        }
    }

    /**
     * Process errors from reCAPTCHA response.
     *
     * @param HttpResponseInterface $response
     * @param array $errorMessages
     * @param string $redirectOnFailureUrl
     * @param string $sourceKey
     * @return void
     */
    private function processError(
        HttpResponseInterface $response,
        array $errorMessages,
        string $redirectOnFailureUrl,
        string $sourceKey
    ): void {
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

        $this->messageManager->addErrorMessage($message);
        $this->actionFlag->set('', Action::FLAG_NO_DISPATCH, true);

        $response->setRedirect($redirectOnFailureUrl);
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
