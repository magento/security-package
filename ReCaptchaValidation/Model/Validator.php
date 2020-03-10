<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaValidation\Model;

use Magento\Framework\Validation\ValidationResult;
use Magento\Framework\Validation\ValidationResultFactory;
use Magento\ReCaptchaValidationApi\Api\ValidatorInterface;
use Magento\ReCaptchaValidationApi\Api\Data\ValidationConfigInterface;
use Magento\ReCaptchaValidationApi\Model\ErrorMessages;
use ReCaptcha\ReCaptcha;

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
     * @var ErrorMessages
     */
    private $errorMessages;

    /**
     * @param ValidationResultFactory $validationResultFactory
     * @param ErrorMessages $errorMessages
     */
    public function __construct(
        ValidationResultFactory $validationResultFactory,
        ErrorMessages $errorMessages
    ) {
        $this->validationResultFactory = $validationResultFactory;
        $this->errorMessages = $errorMessages;
    }

    /**
     * @inheritdoc
     */
    public function isValid(
        string $reCaptchaResponse,
        ValidationConfigInterface $validationConfig
    ): ValidationResult {
        $secret = $validationConfig->getPrivateKey();

        // @codingStandardsIgnoreStart
        $reCaptcha = new ReCaptcha($secret);
        // @codingStandardsIgnoreEnd

        $extensionAttributes = $validationConfig->getExtensionAttributes();
        if ($extensionAttributes && (null !== $extensionAttributes->getScoreThreshold())) {
            $reCaptcha->setScoreThreshold($extensionAttributes->getScoreThreshold());
        }
        $result = $reCaptcha->verify($reCaptchaResponse, $validationConfig->getRemoteIp());

        $validationErrors = [];
        if (!$result->isSuccess()) {
            foreach ($result->getErrorCodes() as $errorCode) {
                $validationErrors[] = $this->errorMessages->getErrorMessage($errorCode);
            }
        }

        return $this->validationResultFactory->create(['errors' => $validationErrors]);
    }
}
