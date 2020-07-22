<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Represents the Google authentication data
 */
interface GoogleAuthenticateInterface extends ExtensibleDataInterface
{
    const OTP = 'otp';

    /**
     * Get the one time password
     *
     * @return string
     */
    public function getOtp(): string;

    /**
     * Set the one time password
     *
     * @param string $value
     * @return void
     */
    public function setOtp(string $value): void;

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * Used fully qualified namespaces in annotations for proper work of extension interface/class code generation
     *
     * @return \Magento\TwoFactorAuth\Api\Data\GoogleAuthenticateExtensionInterface|null
     */
    public function getExtensionAttributes(): ?GoogleAuthenticateExtensionInterface;

    /**
     * Set an extension attributes object
     *
     * @param \Magento\TwoFactorAuth\Api\Data\GoogleAuthenticateExtensionInterface $extensionAttributes
     */
    public function setExtensionAttributes(
        GoogleAuthenticateExtensionInterface $extensionAttributes
    ): void;
}
