<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Api;

use Magento\TwoFactorAuth\Api\Data\DuoDataInterface;

/**
 * Represents configuration for the duo security provider
 */
interface DuoConfigureInterface
{
    /**
     * Get the information required to configure duo
     *
     * @param int $userId
     * @param string $tfaToken
     * @return \Magento\TwoFactorAuth\Api\Data\DuoDataInterface
     */
    public function getConfigurationData(
        int $userId,
        string $tfaToken
    ): DuoDataInterface;

    /**
     * Activate the provider and get an admin token
     *
     * @param int $userId
     * @param string $tfaToken
     * @param string $signatureResponse
     * @return string
     */
    public function activate(int $userId, string $tfaToken, string $signatureResponse): string;
}
