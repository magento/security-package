<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaValidation\Model;

use Magento\Framework\Validation\ValidationResult;
use Magento\Framework\Validation\ValidationResultFactory;
use Magento\ReCaptchaValidationApi\Api\Data\ValidationConfigInterface;
use Magento\ReCaptchaValidationApi\Api\ValidatorInterface;
use Magento\ReCaptchaValidationApi\Model\ErrorMessagesProvider;
use ReCaptcha\ReCaptchaFactory;

/**
 * @inheritdoc
 */
class Validator implements ValidatorInterface
{
    /**
     * @var ValidationResultFactory
     */
    private $validationResultFactory;

    /**
     * @var ErrorMessagesProvider
     */
    private $errorMessagesProvider;

    /**
     * @var ReCaptchaFactory
     */
    private $reCaptchaFactory;

    /**
     * @param ValidationResultFactory $validationResultFactory
     * @param ErrorMessagesProvider $errorMessagesProvider
     * @param ReCaptchaFactory $reCaptchaFactory
     */
    public function __construct(
        ValidationResultFactory $validationResultFactory,
        ErrorMessagesProvider $errorMessagesProvider,
        ReCaptchaFactory $reCaptchaFactory
    ) {
        $this->validationResultFactory = $validationResultFactory;
        $this->errorMessagesProvider = $errorMessagesProvider;
        $this->reCaptchaFactory = $reCaptchaFactory;
    }

    /**
     * @inheritdoc
     */
    public function isValid(
        string $reCaptchaResponse,
        ValidationConfigInterface $validationConfig
    ): ValidationResult {
        $secret = $validationConfig->getPrivateKey();
        $reCaptcha = $this->reCaptchaFactory->create(['secret' => $secret]);
        $extensionAttributes = $validationConfig->getExtensionAttributes();
        if ($extensionAttributes && (null !== $extensionAttributes->getScoreThreshold())) {
            $reCaptcha->setScoreThreshold($extensionAttributes->getScoreThreshold());
        }
        $result = $reCaptcha->verify($reCaptchaResponse, $validationConfig->getRemoteIp());

        if ($result->isSuccess()) {
            $validationResult = $this->validationResultFactory->create(['errors' => []]);
        } else {
            foreach ($result->getErrorCodes() as $errorCode) {
                $validationErrors[] = $this->errorMessagesProvider->getErrorMessage($errorCode);
            }
            $validationResult = $this->validationResultFactory->create(['errors' => $validationErrors]);
        }
        return $validationResult;
    }
}
