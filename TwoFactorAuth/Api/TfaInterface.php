<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Api;

/**
 * 2FA configuration manager.
 *
 * @api
 */
interface TfaInterface
{
    /**
     * Forced providers fields
     */
    public const XML_PATH_FORCED_PROVIDERS = 'twofactorauth/general/force_providers';

    /**
     * Email link URL for webapi-based configuration
     */
    public const XML_PATH_WEBAPI_CONFIG_EMAIL_URL = 'twofactorauth/general/webapi_email_config_url';

    /**
     * Return true if 2FA is enabled
     *
     * @return bool
     */
    public function isEnabled(): bool;

    /**
     * Get provider by code
     *
     * @param string $providerCode
     * @param bool $onlyEnabled = true
     * @return ProviderInterface|null
     */
    public function getProvider(string $providerCode, bool $onlyEnabled = true): ?ProviderInterface;

    /**
     * Retrieve forced providers list
     *
     * @return \Magento\TwoFactorAuth\Api\ProviderInterface[]
     */
    public function getForcedProviders(): array;

    /**
     * Get a user provider
     *
     * @param int $userId
     * @return \Magento\TwoFactorAuth\Api\ProviderInterface[]
     */
    public function getUserProviders(int $userId): array;

    /**
     * Get a list of providers
     *
     * @return \Magento\TwoFactorAuth\Api\ProviderInterface[]
     */
    public function getAllProviders(): array;

    /**
     * Get a list of providers
     *
     * @param string $code
     * @return \Magento\TwoFactorAuth\Api\ProviderInterface|null
     */
    public function getProviderByCode(string $code): ?ProviderInterface;

    /**
     * Get a list of providers
     *
     * @return \Magento\TwoFactorAuth\Api\ProviderInterface[]
     */
    public function getAllEnabledProviders(): array;

    /**
     * Get allowed URLs
     *
     * @return array
     */
    public function getAllowedUrls(): array;

    /**
     * Returns a list of providers to configure/enroll
     *
     * @param int $userId
     * @return \Magento\TwoFactorAuth\Api\ProviderInterface[]
     */
    public function getProvidersToActivate(int $userId): array;

    /**
     * Return true if a provider is allowed for a given user
     *
     * @param int $userId
     * @param string $providerCode
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getProviderIsAllowed(int $userId, string $providerCode): bool;

    /**
     * Get default provider code
     *
     * @param int $userId
     * @return string
     */
    public function getDefaultProviderCode(int $userId): string;

    /**
     * Set default provider code
     *
     * @param int $userId
     * @param string $providerCode
     * @return bool
     */
    public function setDefaultProviderCode(int $userId, string $providerCode): bool;

    /**
     * Set providers
     *
     * @param int $userId
     * @param string $providersCodes
     * @return bool
     */
    public function setProvidersCodes(int $userId, string $providersCodes): bool;

    /**
     * Reset default provider code
     *
     * @param int $userId
     * @param string $providerCode
     * @return bool
     */
    public function resetProviderConfig(int $userId, string $providerCode): bool;
}
