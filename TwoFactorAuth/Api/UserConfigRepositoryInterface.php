<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\TwoFactorAuth\Api\Data\UserConfigInterface;
use Magento\TwoFactorAuth\Api\Data\UserConfigSearchResultsInterface;

/**
 * User configuration repository
 *
 * @api
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
interface UserConfigRepositoryInterface
{
    /**
     * Save object
     *
     * @param UserConfigInterface $object
     * @return UserConfigInterface
     */
    public function save(UserConfigInterface $object): UserConfigInterface;

    /**
     * Get object by id
     *
     * @param int $id
     * @return UserConfigInterface
     */
    public function getById(int $id): UserConfigInterface;

    /**
     * Get by UserId value
     *
     * @param int $value
     * @return UserConfigInterface
     */
    public function getByUserId(int $value): UserConfigInterface;

    /**
     * Delete object
     *
     * @param UserConfigInterface $object
     * @return bool
     */
    public function delete(UserConfigInterface $object): bool;

    /**
     * Get a list of object
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return UserConfigSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface;
}
