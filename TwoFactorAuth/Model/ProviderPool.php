<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\TwoFactorAuth\Api\ProviderInterface;
use Magento\TwoFactorAuth\Api\ProviderPoolInterface;

/**
 * @inheritDoc
 */
class ProviderPool implements ProviderPoolInterface
{
    /**
     * @var ProviderInterface[]
     */
    private $providers;

    /**
     * @param array $providers
     */
    public function __construct(
        array $providers = []
    ) {
        $this->providers = $providers;
    }

    /**
     * @inheritDoc
     */
    public function getProviders(): array
    {
        return $this->providers;
    }

    /**
     * @inheritDoc
     */
    public function getProviderByCode(string $code): ProviderInterface
    {
        if ($code) {
            $providers = $this->getProviders();
            if (isset($providers[$code])) {
                return $providers[$code];
            }
        }

        throw new NoSuchEntityException(__('Unknown provider %1', $code));
    }
}
