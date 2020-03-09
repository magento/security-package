<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaUi\Model;

use Magento\ReCaptchaValidationApi\Api\Data\ValidationConfigInterface;

/**
 * Extension point for adding Validation config for concrete reCAPTCHA type
 *
 * It is NOT direct part of reCAPTCHA validation but performs role of bridge between UI and reCAPTCHA validation
 *
 * @api
 * @see \Magento\ReCaptchaUi\Model\ValidationConfigResolver
 */
interface ValidationConfigProviderInterface
{
    /**
     * Return Validation config for concrete reCAPTCHA type
     *
     * @return ValidationConfigInterface
     */
    public function get(): ValidationConfigInterface;
}
