<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaApi\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\ReCaptchaApi\Api\Data\ValidationConfigInterface;

/**
 * Validate reCAPTCHA response for actual reCAPTCHA version.
 *
 * @api
 */
interface CaptchaTypeValidatorInterface
{
    /**
     * Return true if reCAPTCHA validation has passed
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
