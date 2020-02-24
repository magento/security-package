<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaApi\Api;

/**
 * Return true if type of reCAPTCHA are invisible (for example 'invisible' or 'recaptcha_v3')
 *
 * Extension point for adding new invisible reCAPTCHA types
 *
 * @api
 */
interface IsInvisibleCaptchaInterface
{
    /**
     * Return true if type of reCAPTCHA are invisible (for example 'invisible' or 'recaptcha_v3')
     *
     * @param string $captchaType
     * @return bool
     */
    public function isInvisible(string $captchaType): bool;
}
