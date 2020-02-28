<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaUi\Model;

use Magento\Framework\Exception\InputException;

/**
 * Extension point for reCAPTCHA UI config
 *
 * @api
 */
interface UiConfigResolverInterface
{
    /**
     * Resolve UI config for reCAPTCHA rendering
     *
     * @param string $key Functionality identifier (like customer login, contact)
     * @return array
     * @throws InputException If UI config for "%key" is not configured
     */
    public function get(string $key): array;
}
