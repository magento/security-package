<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Api;

use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Providers pool
 *
 * @api
 */
interface ProviderPoolInterface
{
    /**
     * Get a list of providers
     *
     * @return \Magento\TwoFactorAuth\Api\ProviderInterface[]
     */
    public function getProviders(): array;

    /**
     * Get provider by code
     *
     * @param string $code
     * @return \Magento\TwoFactorAuth\Api\ProviderInterface
     * @throws NoSuchEntityException
     */
    public function getProviderByCode(string $code): ProviderInterface;
}
