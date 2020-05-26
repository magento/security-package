<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Response for device registration prompt
 */
interface AuthyRegistrationPromptResponseInterface extends ExtensibleDataInterface
{
    /**
     * Message field
     */
    const MESSAGE = 'message';

    /**
     * Field for how many seconds until prompt expires
     */
    const EXPIRATION_SECONDS = 'seconds_to_expire';

    /**
     * Get the message
     *
     * @return string
     */
    public function getMessage(): string;

    /**
     * Set the message
     *
     * @param string $value
     * @return void
     */
    public function setMessage(string $value): void;

    /**
     * Get the seconds to expire
     *
     * @return string
     */
    public function getExpirationSeconds(): int;

    /**
     * Set the seconds to expire
     *
     * @param string $value
     * @return void
     */
    public function setExpirationSeconds(int $value): void;

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * Used fully qualified namespaces in annotations for proper work of extension interface/class code generation
     *
     * @return \Magento\TwoFactorAuth\Api\Data\AuthyRegistrationPromptResponseExtensionInterface|null
     */
    public function getExtensionAttributes(): ?AuthyRegistrationPromptResponseExtensionInterface;

    /**
     * Set an extension attributes object
     *
     * @param \Magento\TwoFactorAuth\Api\Data\AuthyRegistrationPromptResponseExtensionInterface $extensionAttributes
     * @return void
     */
    public function setExtensionAttributes(
        AuthyRegistrationPromptResponseExtensionInterface $extensionAttributes
    ): void;
}
