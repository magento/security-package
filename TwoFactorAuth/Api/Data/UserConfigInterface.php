<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * User configuration interface
 *
 * @api
 */
interface UserConfigInterface extends ExtensibleDataInterface
{
    /**
     * Entity ID field name
     */
    public const ID = 'config_id';

    /**
     * User ID field name
     */
    public const USER_ID = 'user_id';

    /**
     * Encoded providers filed name
     */
    public const ENCODED_PROVIDERS = 'encoded_providers';

    /**
     * Selected default provider field name
     */
    public const DEFAULT_PROVIDER = 'default_provider';

    /**
     * Get value for config_id
     *
     * @return int
     */
    public function getId(): int;

    /**
     * Set value for config_id
     *
     * @param int $value
     */
    public function setId($value): void;

    /**
     * Get value for user_id
     *
     * @return int
     */
    public function getUserId(): int;

    /**
     * Set value for user_id
     *
     * @param int $value
     */
    public function setUserId(int $value): void;

    /**
     * Get value for encoded_providers
     *
     * @return string
     */
    public function getEncodedProviders(): string;

    /**
     * Set value for encoded_providers
     *
     * @param string $value
     */
    public function setEncodedProviders(string $value): void;

    /**
     * Get value for default_provider
     *
     * @return string
     */
    public function getDefaultProvider(): string;

    /**
     * Set value for default_provider
     *
     * @param string $value
     */
    public function setDefaultProvider(string $value): void;

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * Used fully qualified namespaces in annotations for proper work of extension interface/class code generation
     *
     * @return \Magento\TwoFactorAuth\Api\Data\UserConfigExtensionInterface|null
     */
    public function getExtensionAttributes(): ?UserConfigExtensionInterface;

    /**
     * Set an extension attributes object
     *
     * @param \Magento\TwoFactorAuth\Api\Data\UserConfigExtensionInterface $extensionAttributes
     */
    public function setExtensionAttributes(UserConfigExtensionInterface $extensionAttributes): void;
}
