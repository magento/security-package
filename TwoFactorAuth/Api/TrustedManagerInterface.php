<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Api;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Trusted management service
 */
interface TrustedManagerInterface
{
    /**
     * Cookie name for trusted device
     */
    public const TRUSTED_DEVICE_COOKIE = 'tfa_trusted';

    /**
     * Rotate secret trust token
     * @return void
     */
    public function rotateTrustedDeviceToken(): void;

    /**
     * Return true if device is trusted
     * @return bool
     */
    public function isTrustedDevice(): bool;

    /**
     * Revoke trusted device
     * @param int $tokenId
     * @return void
     * @throws NoSuchEntityException
     */
    public function revokeTrustedDevice(int $tokenId): void;

    /**
     * Trust a device
     * @param string $providerCode
     * @param RequestInterface $request
     * @return bool
     */
    public function handleTrustDeviceRequest(string $providerCode, RequestInterface $request): bool;
}
