<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Api;

/**
 * Represents configuration for u2f key provider
 *
 * @api
 */
interface U2fKeyConfigReaderInterface
{
    /**
     * Get the domain to use for WebAuthn ceremonies
     *
     * @return string
     */
    public function getDomain(): string;
}
