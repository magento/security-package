<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaNewsletter\Model;

/**
 * Return true if functionality of corresponding point is enabled in configuration
 *
 * @api
 */
interface IsEnabledForNewsletterInterface
{
    /**
     * Return true if recaptcha is enabled for newsletter form
     * @return bool
     */
    public function isEnabled(): bool;
}
