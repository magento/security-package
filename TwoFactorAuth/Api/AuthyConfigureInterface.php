<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Api;

use Magento\TwoFactorAuth\Api\Data\AuthyDeviceInterface;
use Magento\TwoFactorAuth\Api\Data\AuthyRegistrationPromptResponseInterface as ResponseInterface;

/**
 * Represents the authy provider
 */
interface AuthyConfigureInterface
{
    /**
     * Get the information required to configure google
     *
     * @param string $tfaToken
     * @param AuthyDeviceInterface $deviceData
     * @return \Magento\TwoFactorAuth\Api\Data\AuthyRegistrationPromptResponseInterface
     */
    public function sendDeviceRegistrationPrompt(
        string $tfaToken,
        AuthyDeviceInterface $deviceData
    ): ResponseInterface;

    /**
     * Activate the provider and get an admin token
     *
     * @param string $tfaToken
     * @param string $otp
     * @return void
     */
    public function activate(string $tfaToken, string $otp): void;
}
