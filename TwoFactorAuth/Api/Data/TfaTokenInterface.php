<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Represents the 2fa token data required to make 2fa changes
 */
interface TfaTokenInterface extends ExtensibleDataInterface
{
    const TOKEN = 'token';

    /**
     * Get the two-factor token needed to make 2fa configuration changes
     *
     * @return string
     */
    public function getToken(): string;

    /**
     * Set the two-factor token needed to make 2fa configuration changes
     *
     * @param string $value
     * @return void
     */
    public function setToken(string $value): void;

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * Used fully qualified namespaces in annotations for proper work of extension interface/class code generation
     *
     * @return \Magento\TwoFactorAuth\Api\Data\TfaTokenExtensionInterface|null
     */
    public function getExtensionAttributes(): ?TfaTokenExtensionInterface;

    /**
     * Set an extension attributes object
     *
     * @param \Magento\TwoFactorAuth\Api\Data\TfaTokenExtensionInterface $extensionAttributes
     */
    public function setExtensionAttributes(
        TfaTokenExtensionInterface $extensionAttributes
    ): void;
}
