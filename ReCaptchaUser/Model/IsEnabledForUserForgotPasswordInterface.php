<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaUser\Model;

/**
 * Return true if functionality of corresponding point is enabled in configuration
 *
 * @api
 */
interface IsEnabledForUserForgotPasswordInterface
{
    /**
     * Return true if recaptcha is enabled for user forgot password
     * @return bool
     */
    public function isEnabled(): bool;
}
