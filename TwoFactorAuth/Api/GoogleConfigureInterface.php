<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Api;

use Magento\TwoFactorAuth\Api\Data\TfaTokenInterface;

/**
 * Represents the google provider
 */
interface GoogleConfigureInterface
{
    /**
     * Get the information required to configure google
     *
     * @param int $userId
     * @param TfaTokenInterface $tfaToken
     * @return \Magento\TwoFactorAuth\Api\Data\GoogleConfigureInterface
     */
    public function getConfigurationData(
        int $userId,
        TfaTokenInterface $tfaToken
    ): \Magento\TwoFactorAuth\Api\Data\GoogleConfigureInterface;
}
