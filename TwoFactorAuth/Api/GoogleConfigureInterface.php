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
     * @param string $tfat
     * @return \Magento\TwoFactorAuth\Api\Data\GoogleConfigureInterface
     */
    public function getConfigurationData(
        int $userId,
        string $tfat
    ): \Magento\TwoFactorAuth\Api\Data\GoogleConfigureInterface;
}
