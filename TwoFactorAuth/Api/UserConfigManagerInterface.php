<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Api;

use Magento\Framework\Exception\NoSuchEntityException;

/**
 * User configuration manager
 */
interface UserConfigManagerInterface
{
    /**
     * Active configuration key
     */
    public const ACTIVE_CONFIG_KEY = 'active';

    /**
     * Get a provider configuration for a given user
     *
     * @param int $userId
     * @param string $providerCode
     * @return array|null
     * @throws NoSuchEntityException
     */
    public function getProviderConfig(int $userId, string $providerCode): ?array;

    /**
     * Set provider configuration
     *
     * @param int $userId
     * @param string $providerCode
     * @param array|null $config
     * @return bool
     * @throws NoSuchEntityException
     */
    public function setProviderConfig(int $userId, string $providerCode, ?array $config = null): bool;

    /**
     * Set provider configuration
     *
     * @param int $userId
     * @param string $providerCode
     * @param array|null $config
     * @return bool
     * @throws NoSuchEntityException
     */
    public function addProviderConfig(int $userId, string $providerCode, ?array $config = null): bool;

    /**
     * Reset provider configuration
     *
     * @param int $userId
     * @param string $providerCode
     * @return bool
     * @throws NoSuchEntityException
     */
    public function resetProviderConfig(int $userId, string $providerCode): bool;

    /**
     * Set providers list for a given user
     *
     * @param int $userId
     * @param string|array $providersCodes
     * @return bool
     * @throws NoSuchEntityException
     */
    public function setProvidersCodes(int $userId, $providersCodes): bool;

    /**
     * Set providers list for a given user
     *
     * @param int $userId
     * @return string[]
     */
    public function getProvidersCodes(int $userId): array;

    /**
     * Activate a provider configuration
     *
     * @param int $userId
     * @param string $providerCode
     * @return bool
     * @throws NoSuchEntityException
     */
    public function activateProviderConfiguration(int $userId, string $providerCode): bool;

    /**
     * Return true if a provider configuration has been activated
     *
     * @param int $userId
     * @param string $providerCode
     * @return bool
     * @throws NoSuchEntityException
     */
    public function isProviderConfigurationActive(int $userId, string $providerCode): bool;

    /**
     * Set default provider
     *
     * @param int $userId
     * @param string $providerCode
     * @return bool
     * @throws NoSuchEntityException
     */
    public function setDefaultProvider(int $userId, string $providerCode): bool;

    /**
     * Get default provider
     *
     * @param int $userId
     * @return string
     */
    public function getDefaultProvider(int $userId): string;
}
