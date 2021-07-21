<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Api;

/**
 * Represents the authy provider authentication
 *
 * @api
 */
interface AuthyAuthenticateInterface
{
    /**
     * Get an admin token using authy 2fa
     *
     * @param string $username
     * @param string $password
     * @param string $otp
     * @return string $otp
     */
    public function createAdminAccessTokenWithCredentials(
        string $username,
        string $password,
        string $otp
    ): string;

    /**
     * Send a one time password to a device using authy
     *
     * @param string $username
     * @param string $password
     * @param string $via
     * @return void
     */
    public function sendToken(
        string $username,
        string $password,
        string $via
    ): void;

    /**
     * Authenticate using the present one touch response and get an admin token
     *
     * @param string $username
     * @param string $password
     * @return string
     */
    public function creatAdminAccessTokenWithOneTouch(
        string $username,
        string $password
    ): string;
}
