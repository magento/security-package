<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Api;

use Magento\TwoFactorAuth\Api\Data\U2fWebAuthnRequestInterface;

/**
 * Represent Authentication for u2f key provider
 */
interface U2fKeyAuthenticateInterface
{
    /**
     * Get the information to initiate a WebAuthn registration ceremony
     *
     * @param string $username
     * @param string $password
     * @return \Magento\TwoFactorAuth\Api\Data\U2fWebAuthnRequestInterface
     */
    public function getAuthenticationData(string $username, string $password): U2fWebAuthnRequestInterface;

    /**
     * Authenticate with the provider and get a token
     *
     * @param string $username
     * @param string $password
     * @param string $publicKeyCredentialJson
     * @return string
     */
    public function createAdminAccessToken(
        string $username,
        string $password,
        string $publicKeyCredentialJson
    ): string;
}
