<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\TwoFactorAuth\Api\Data\TrustedInterface;

/**
 * Trusted repository
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
interface TrustedRepositoryInterface
{
    /**
     * Save object
     * @param TrustedInterface $object
     * @return TrustedInterface
     */
    public function save(TrustedInterface $object): TrustedInterface;

    /**
     * Get object by id
     * @param int $id
     * @return TrustedInterface
     */
    public function getById(int $id): TrustedInterface;

    /**
     * Get by UserId value
     * @param int $value
     * @return TrustedInterface
     */
    public function getByUserId(int $value): TrustedInterface;

    /**
     * Delete object
     * @param TrustedInterface $object
     */
    public function delete(TrustedInterface $object): void;

    /**
     * Get a list of object
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface;
}
