<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Represents a WebAuthn dataset
 */
interface U2fWebAuthnRequestInterface extends ExtensibleDataInterface
{
    const CREDENTIAL_REQUEST_OPTIONS_JSON = 'credential_request_options_json';

    /**
     * Get the needed data to initiate a WebAuthn registration ceremony
     *
     * @return string
     */
    public function getCredentialRequestOptionsJson(): string;

    /**
     * Set the needed data to initiate a WebAuthn registration ceremony
     *
     * @param string $value
     * @return void
     */
    public function setCredentialRequestOptionsJson(string $value): void;

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * Used fully qualified namespaces in annotations for proper work of extension interface/class code generation
     *
     * @return \Magento\TwoFactorAuth\Api\Data\U2fWebAuthnRequestExtensionInterface|null
     */
    public function getExtensionAttributes(): ?U2fWebAuthnRequestExtensionInterface;

    /**
     * Set an extension attributes object
     *
     * @param \Magento\TwoFactorAuth\Api\Data\U2fWebAuthnRequestExtensionInterface $extensionAttributes
     * @return void
     */
    public function setExtensionAttributes(
        U2fWebAuthnRequestExtensionInterface $extensionAttributes
    ): void;
}
