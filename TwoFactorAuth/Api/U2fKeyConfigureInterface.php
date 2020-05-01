<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Api;

use Magento\TwoFactorAuth\Api\Data\U2fWebAuthnRequestInterface;

/**
 * Represent configuration for u2f key provider
 */
interface U2fKeyConfigureInterface
{
    /**
     * Get the information to initiate a WebAuthn registration ceremony
     *
     * @param string $tfaToken
     * @return \Magento\TwoFactorAuth\Api\Data\U2fWebAuthnRequestInterface
     */
    public function getRegistrationData(string $tfaToken): U2fWebAuthnRequestInterface;

    /**
     * Activate the provider and get a token
     *
     * @param string $tfaToken
     * @param string $publicKeyCredentialJson
     * @return void
     */
    public function activate(string $tfaToken, string $publicKeyCredentialJson): void;
}
