<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaWebapiGraphQl\Plugin;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\Validation\ValidationResult;
use Magento\ReCaptchaValidationApi\Api\Data\ValidationConfigInterface;
use Magento\ReCaptchaValidationApi\Api\ValidatorInterface;

/**
 * Override validation result for certain web api cases.
 */
class ValidationOverrider
{
    /**
     * @var UserContextInterface
     */
    private $userContext;

    /**
     * @param UserContextInterface $userContext
     */
    public function __construct(UserContextInterface $userContext)
    {
        $this->userContext = $userContext;
    }

    /**
     * Override isValid().
     *
     * @param ValidatorInterface $subject
     * @param callable $proceed
     * @param string $value
     * @param ValidationConfigInterface $config
     * @return ValidationResult
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundIsValid(
        ValidatorInterface $subject,
        callable $proceed,
        string $value,
        ValidationConfigInterface $config
    ): ValidationResult {
        if ($this->userContext->getUserType() === UserContextInterface::USER_TYPE_INTEGRATION) {
            return new ValidationResult([]);
        }

        return $proceed($value, $config);
    }
}
