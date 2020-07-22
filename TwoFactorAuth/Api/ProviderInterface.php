<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Api;

/**
 * 2FA proviced interface
 */
interface ProviderInterface
{
    /**
     * Return true if this provider has been enabled by admin
     *
     * @return bool
     */
    public function isEnabled(): bool;

    /**
     * Get provider engine
     *
     * @return \Magento\TwoFactorAuth\Api\EngineInterface
     */
    public function getEngine();

    /**
     * Get provider code
     *
     * @return string
     */
    public function getCode(): string;

    /**
     * Get provider name
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get icon
     *
     * @return string
     */
    public function getIcon(): string;

    /**
     * Return true if this provider configuration can be reset
     *
     * @return bool
     */
    public function isResetAllowed(): bool;

    /**
     * Reset provider configuration
     *
     * @param int $userId
     * @return void
     */
    public function resetConfiguration(int $userId): void;

    /**
     * Return true if this provider has been configured
     *
     * @param int $userId
     * @return bool
     */
    public function isConfigured(int $userId): bool;

    /**
     * Return true if current provider has been activated
     *
     * @param int $userId
     * @return bool
     */
    public function isActive(int $userId): bool;

    /**
     * Activate provider
     *
     * @param int $userId
     * @return void
     */
    public function activate(int $userId): void;

    /**
     * Get configure action
     *
     * @return string
     */
    public function getConfigureAction(): string;

    /**
     * Get auth action
     *
     * @return string
     */
    public function getAuthAction(): string;

    /**
     * Get allowed extra actions
     *
     * @return string[]
     */
    public function getExtraActions(): array;
}
