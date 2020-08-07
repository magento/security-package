<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaCustomer\Model\AjaxLogin;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\ReCaptchaUi\Model\ErrorMessageConfigInterface;
use Magento\ReCaptchaValidationApi\Model\ValidationErrorMessagesProvider;
use Psr\Log\LoggerInterface;

/**
 * Process error during ajax login
 *
 * Set "no dispatch" flag and error message to Response
 */
class ErrorProcessor
{
    /**
     * @var ActionFlag
     */
    private $actionFlag;

    /**
     * @var SerializerInterface
     */
    private $serializer;

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
     * @param ActionFlag $actionFlag
     * @param SerializerInterface $serializer
     * @param LoggerInterface|null $logger
     * @param ErrorMessageConfigInterface|null $errorMessageConfig
     * @param ValidationErrorMessagesProvider|null $validationErrorMessagesProvider
     */
    public function __construct(
        ActionFlag $actionFlag,
        SerializerInterface $serializer,
        ?LoggerInterface $logger = null,
        ?ErrorMessageConfigInterface $errorMessageConfig = null,
        ?ValidationErrorMessagesProvider $validationErrorMessagesProvider = null
    ) {
        $this->actionFlag = $actionFlag;
        $this->serializer = $serializer;
        $this->logger = $logger
            ?? ObjectManager::getInstance()->get(LoggerInterface::class);
        $this->errorMessageConfig = $errorMessageConfig
            ?? ObjectManager::getInstance()->get(ErrorMessageConfigInterface::class);
        $this->validationErrorMessagesProvider = $validationErrorMessagesProvider
            ?? ObjectManager::getInstance()->get(ValidationErrorMessagesProvider::class);
    }

    /**
     * Set "no dispatch" flag and error message to Response
     *
     * @param ResponseInterface $response
     * @param array $errorMessages
     * @param string $sourceKey
     * @return void
     */
    public function processError(ResponseInterface $response, array $errorMessages, string $sourceKey): void
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
            'errors' => true,
            'message' => $message,
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
