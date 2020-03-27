<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Api;

/**
 * Represents the google provider
 */
interface GoogleAuthenticateInterface
{
    /**
     * Get an admin token by authenticating using google
     *
     * @param int $userId
     * @param string $otp
     * @return string
     */
    public function getToken(int $userId, string $otp): string;
}
