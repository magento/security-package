<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptcha\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\ReCaptchaApi\Api\CaptchaValidatorInterface;
use Magento\ReCaptchaApi\Api\Data\ValidationConfigInterface;

/**
 * @inheritDoc
 */
class CaptchaValidator implements CaptchaValidatorInterface
{
    /**
     * @var CaptchaTypeValidatorInterface[]
     */
    private $validators;

    /**
     * @param CaptchaTypeValidatorInterface[] $validators
     */
    public function __construct(
        array $validators = []
    ) {
        $this->validators = $validators;
    }

    /**
     * @inheritdoc
     */
    public function isValid(
        string $reCaptchaResponse,
        ValidationConfigInterface $validationConfig
    ): bool {
        /**
         * @var string $validatorCode
         * @var CaptchaTypeValidatorInterface $validatorInstance
         */
        foreach ($this->validators as $validatorCode => $validatorInstance) {
            if ($validatorCode === $validationConfig->getCaptchaType()) {
                return $validatorInstance->validate($reCaptchaResponse, $validationConfig);
            }
        }

        throw new LocalizedException(__('No reCAPTCHA validator found.'));
    }
}
