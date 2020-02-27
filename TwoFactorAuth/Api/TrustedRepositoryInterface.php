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
 * @deprecated Trusted Devices functionality was removed.
 */
interface TrustedRepositoryInterface
{
    /**
     * Save object
     *
     * @param TrustedInterface $object
     * @return TrustedInterface
     * @deprecated Trusted Devices functionality was removed.
     */
    public function save(TrustedInterface $object): TrustedInterface;

    /**
     * Get object by id
     *
     * @param int $id
     * @return TrustedInterface
     * @deprecated Trusted Devices functionality was removed.
     */
    public function getById(int $id): TrustedInterface;

    /**
     * Get by UserId value
     *
     * @param int $value
     * @return TrustedInterface
     * @deprecated Trusted Devices functionality was removed.
     */
    public function getByUserId(int $value): TrustedInterface;

    /**
     * Delete object
     *
     * @param TrustedInterface $object
     * @return void
     * @deprecated Trusted Devices functionality was removed.
     */
    public function delete(TrustedInterface $object): void;

    /**
     * Get a list of object
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchResultsInterface
     * @deprecated Trusted Devices functionality was removed.
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface;
}
