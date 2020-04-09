<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Api;

use Magento\TwoFactorAuth\Api\Data\U2FWebAuthnRequestInterface;

/**
 * Represent configuration for u2f key provider
 */
interface U2fKeyConfigureInterface
{
    /**
     * Get the information to initiate a WebAuthn registration ceremony
     *
     * @param string $tfaToken
     * @return U2FWebAuthnRequestInterface
     */
    public function getRegistrationData(string $tfaToken): U2FWebAuthnRequestInterface;

    /**
     * Activate the provider and get a token
     *
     * @param string $tfaToken
     * @param string $publicKeyCredentialJson
     * @return bool
     */
    public function activate(string $tfaToken, string $publicKeyCredentialJson): bool;
}
