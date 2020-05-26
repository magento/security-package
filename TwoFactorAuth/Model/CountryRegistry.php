<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model;

use Magento\TwoFactorAuth\Api\Data\CountryInterface;

/**
 * Country entity registry
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class CountryRegistry
{
    /**
     * @var array
     */
    private $registry = [];

    /**
     * @var array
     */
    private $registryByKey = [
        'code' => [],
    ];

    /**
     * Remove registry entity by id
     *
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
     *
     * @param int $id
     * @return CountryInterface|null
     */
    public function retrieveById(int $id): ?CountryInterface
    {
        if (isset($this->registry[$id])) {
            return $this->registry[$id];
        }

        return null;
    }

    /**
     * Retrieve by Code value
     *
     * @param string $value
     * @return CountryInterface|null
     */
    public function retrieveByCode(string $value): ?CountryInterface
    {
        if (isset($this->registryByKey['code'][$value])) {
            return $this->retrieveById($this->registryByKey['code'][$value]);
        }

        return null;
    }

    /**
     * Push one object into registry
     *
     * @param Country $country
     */
    public function push(Country $country): void
    {
        $this->registry[$country->getId()] = $country->getDataModel();
        foreach (array_keys($this->registryByKey) as $key) {
            $this->registryByKey[$key][$country->getData($key)] = $country->getId();
        }
    }
}
