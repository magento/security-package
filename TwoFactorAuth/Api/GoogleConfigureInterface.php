<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Api;

/**
 * Represents the google provider
 *
 * @api
 */
interface GoogleConfigureInterface
{
    /**
     * Get the information required to configure google
     *
     * @param string $tfaToken
     * @return \Magento\TwoFactorAuth\Api\Data\GoogleConfigureInterface
     */
    public function getConfigurationData(
        string $tfaToken
    ): \Magento\TwoFactorAuth\Api\Data\GoogleConfigureInterface;

    /**
     * Activate the provider and get an admin token
     *
     * @param string $tfaToken
     * @param string $otp
     * @return void
     */
    public function activate(string $tfaToken, string $otp): void;
}
