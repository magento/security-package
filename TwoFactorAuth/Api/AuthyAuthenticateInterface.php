<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Api;

use Magento\TwoFactorAuth\Api\Data\AuthyDeviceInterface;

/**
 * Represents the authy provider authentication
 */
interface AuthyAuthenticateInterface
{
    /**
     * Get an admin token using authy 2fa
     *
     * @param string $username
     * @param string $password
     * @return string $otp
     */
    public function authenticateWithToken(
        string $username,
        string $password,
        string $otp
    ): string;

    /**
     * Send a token to a device using authy
     *
     * @param string $username
     * @param string $password
     * @param string $via
     * @return bool
     */
    public function sendToken(
        string $username,
        string $password,
        string $via
    ): bool;

    /**
     * Authenticate using the present one touch response and get an admin token
     *
     * @param string $username
     * @param string $password
     * @return string
     */
    public function authenticateWithOnetouch(
        string $username,
        string $password
    ): string;
}
