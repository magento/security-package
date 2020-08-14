<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model;

use Magento\TwoFactorAuth\Api\Data\UserConfigInterface;

/**
 * User configuration registry
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class UserConfigRegistry
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
     * @return UserConfigInterface|null
     */
    public function retrieveById(int $id): ?UserConfigInterface
    {
        return $this->registry[$id] ?? null;
    }

    /**
     * Retrieve by UserId value
     *
     * @param int $value
     * @return UserConfigInterface|null
     */
    public function retrieveByUserId(int $value): ?UserConfigInterface
    {
        if (isset($this->registryByKey['user_id'][$value])) {
            return $this->retrieveById($this->registryByKey['user_id'][$value]);
        }

        return null;
    }

    /**
     * Push one object into registry
     *
     * @param UserConfig $userConfig
     */
    public function push(UserConfig $userConfig): void
    {
        $this->registry[$userConfig->getId()] = $userConfig->getDataModel();
        foreach (array_keys($this->registryByKey) as $key) {
            $this->registryByKey[$key][$userConfig->getData($key)] = $userConfig->getId();
        }
    }
}
