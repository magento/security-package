<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaUi\Model;

use Magento\Framework\Exception\InputException;

/**
 * Return true if reCAPTCHA is enabled for specific functionality
 *
 * @api
 */
interface IsCaptchaEnabledInterface
{
    /**
     * Return true if reCAPTCHA is enabled for specific functionality
     *
     * @param string $key Functionality identifier (like customer login, contact)
     * @return bool
     * @throws InputException
     */
    public function isCaptchaEnabledFor(string $key): bool;
}
