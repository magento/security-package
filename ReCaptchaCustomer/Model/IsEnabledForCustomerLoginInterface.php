<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaCustomer\Model;

/**
 * Return true if functionality of corresponding point is enabled in configuration
 *
 * @api
 */
interface IsEnabledForCustomerLoginInterface
{
    /**
     * Return true if recaptcha is enabled for customer login
     * @return bool
     */
    public function isEnabled(): bool;
}
