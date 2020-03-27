<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Api;

/**
 * Represents the google provider
 */
interface GoogleConfigureInterface
{
    /**
     * Get the information required to configure google
     *
     * @param int $userId
     * @param string $tfaToken
     * @return \Magento\TwoFactorAuth\Api\Data\GoogleConfigureInterface
     */
    public function getConfigurationData(
        int $userId,
        string $tfaToken
    ): \Magento\TwoFactorAuth\Api\Data\GoogleConfigureInterface;

    /**
     * Activate the provider and get an admin token
     *
     * @param int $userId
     * @param string $tfaToken
     * @param string $otp
     * @return string
     */
    public function activate(int $userId, string $tfaToken, string $otp): string;
}
