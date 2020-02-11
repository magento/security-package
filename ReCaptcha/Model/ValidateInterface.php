<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptcha\Model;

use Magento\Framework\Exception\LocalizedException;

/**
 * Validate recaptcha response
 *
 * @api
 */
interface ValidateInterface
{
    /**
     * Parameter name for recaptcha response
     */
    public const PARAM_RECAPTCHA_RESPONSE = 'g-recaptcha-response';

    /**
     * Return true if reCaptcha validation has passed
     *
     * @param string $reCaptchaResponse
     * @param ValidationConfigInterface $validationConfig
     * @return bool
     * @throws LocalizedException
     */
    public function validate(
        string $reCaptchaResponse,
        ValidationConfigInterface $validationConfig
    ): bool;
}
