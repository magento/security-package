<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Api;

/**
 * Actions the user can perform using the pre-2fa token (tfat)
 *
 * @api
 */
interface TfatActionsInterface
{
    /**
     * Get the providers that the user is able to use for 2fa
     *
     * @param string $tfaToken
     * @return \Magento\TwoFactorAuth\Api\ProviderInterface[]
     */
    public function getUserProviders(string $tfaToken): array;

    /**
     * Get the providers that the user still needs to configure
     *
     * @param string $tfaToken
     * @return \Magento\TwoFactorAuth\Api\ProviderInterface[]
     */
    public function getProvidersToActivate(string $tfaToken): array;
}
