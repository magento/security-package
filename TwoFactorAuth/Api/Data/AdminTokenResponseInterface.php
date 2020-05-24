<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Represents the response to the new admin token response
 */
interface AdminTokenResponseInterface extends ExtensibleDataInterface
{
    /**
     * User id field
     */
    const USER_ID = 'user_id';

    /**
     * Message field
     */
    const MESSAGE = 'message';

    /**
     * Providers field
     */
    const ACTIVE_PROVIDERS = 'active_providers';

    /**
     * Get the id of the authenticated user
     *
     * @return string
     */
    public function getUserId(): string;

    /**
     * Set the id of the authenticated user
     *
     * @param int $value
     * @return void
     */
    public function setUserId(int $value): void;

    /**
     * Get the message from the response
     *
     * @return string
     */
    public function getMessage(): string;

    /**
     * Set the id of the message
     *
     * @param string $value
     * @return void
     */
    public function setMessage(string $value): void;

    /**
     * Get the providers
     *
     * @return \Magento\TwoFactorAuth\Api\ProviderInterface[]
     */
    public function getActiveProviders(): array;

    /**
     * Set the providers
     *
     * @param \Magento\TwoFactorAuth\Api\ProviderInterface[] $value
     * @return void
     */
    public function setActiveProviders(array $value): void;

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * Used fully qualified namespaces in annotations for proper work of extension interface/class code generation
     *
     * @return \Magento\TwoFactorAuth\Api\Data\AdminTokenResponseExtensionInterface|null
     */
    public function getExtensionAttributes(): ?AdminTokenResponseExtensionInterface;

    /**
     * Set an extension attributes object
     *
     * @param \Magento\TwoFactorAuth\Api\Data\AdminTokenResponseExtensionInterface $extensionAttributes
     * @return void
     */
    public function setExtensionAttributes(
        AdminTokenResponseExtensionInterface $extensionAttributes
    ): void;
}
