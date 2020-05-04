<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaUi\Model;

use Magento\Framework\Exception\InputException;
use Magento\ReCaptchaValidationApi\Api\Data\ValidationConfigInterface;

/**
 * Extension point for resolving reCAPTCHA Validation config
 *
 * It is NOT direct part of reCAPTCHA validation but performs role of bridge between UI and reCAPTCHA validation
 *
 * @api
 */
interface ValidationConfigResolverInterface
{
    /**
     * Resolve reCAPTCHA Validation config
     *
     * @param string $key Functionality identifier (like customer login, contact)
     * @return ValidationConfigInterface
     * @throws InputException If Validation config for "%key" is not configured
     */
    public function get(string $key): ValidationConfigInterface;
}
