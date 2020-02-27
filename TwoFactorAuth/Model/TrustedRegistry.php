<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model;

use Magento\TwoFactorAuth\Api\Data\TrustedInterface;

/**
 * Trusted hosts registry
 * @SuppressWarnings(PHPMD.ShortVariable)
 * @deprecated Trusted Devices functionality was removed.
 */
class TrustedRegistry
{
    /**
     * @var array
     */
    private $registry = [];

    /**
     * @var array
     */
    private $registryByKey = [
        'user_id' => [],
    ];

    /**
     * Remove registry entity by id
     * @param int $id
     */
    public function removeById(int $id): void
    {
        if (isset($this->registry[$id])) {
            unset($this->registry[$id]);
        }

        foreach (array_keys($this->registryByKey) as $key) {
            $reverseMap = array_flip($this->registryByKey[$key]);
            if (isset($reverseMap[$id])) {
                unset($this->registryByKey[$key][$reverseMap[$id]]);
            }
        }
    }

    /**
     * Push one object into registry
     * @param int $id
     * @return TrustedInterface|null
     */
    public function retrieveById(int $id): ?TrustedInterface
    {
        if (isset($this->registry[$id])) {
            return $this->registry[$id];
        }

        return null;
    }

    /**
     * Retrieve by UserId value
     * @param int $value
     * @return TrustedInterface|null
     */
    public function retrieveByUserId(int $value): ?TrustedInterface
    {
        if (isset($this->registryByKey['user_id'][$value])) {
            return $this->retrieveById($this->registryByKey['user_id'][$value]);
        }

        return null;
    }

    /**
     * Push one object into registry
     * @param Trusted $trusted
     */
    public function push(Trusted $trusted): void
    {
        $this->registry[$trusted->getId()] = $trusted->getDataModel();
        foreach (array_keys($this->registryByKey) as $key) {
            $this->registryByKey[$key][$trusted->getData($key)] = $trusted->getId();
        }
    }
}
