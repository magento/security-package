<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Api;

/**
 * 2FA session interface
 */
interface TfaSessionInterface
{
    /**
     * Passed 2FA key name
     */
    public const KEY_PASSED = '2fa_passed';

    /**
     * Set 2FA session as passed
     */
    public function grantAccess(): void;

    /**
     * Return true if 2FA session has been passed
     *
     * @return bool
     */
    public function isGranted(): bool;

    /**
     * Get the current configuration for skipped providers
     *
     * @return array
     */
    public function getSkippedProviderConfig(): array;

    /**
     * Set the configuration of skipped providers
     *
     * @param array $config
     */
    public function setSkippedProviderConfig(array $config): void;
}
