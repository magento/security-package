<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Api;

/**
 * Actions the user can perform using the pre-2fa token (tfat)
 */
interface TfatActionsInterface
{
    /**
     * Get the providers that the user is able to use for 2fa
     *
     * @param int $userId
     * @param string $tfaToken
     * @return \Magento\TwoFactorAuth\Api\ProviderInterface[]
     */
    public function getUserProviders(int $userId, string $tfaToken): array;

    /**
     * Get the providers that the user still needs to configure
     *
     * @param int $userId
     * @param string $tfaToken
     * @return \Magento\TwoFactorAuth\Api\ProviderInterface[]
     */
    public function getProvidersToActivate(int $userId, string $tfaToken): array;
}
