<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\TwoFactorAuth\Api\Data\CountryInterface;
use Magento\TwoFactorAuth\Api\Data\CountrySearchResultsInterface;

/**
 * Countries repository
 *
 * @api
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
interface CountryRepositoryInterface
{
    /**
     * Save object
     *
     * @param CountryInterface $object
     * @return CountryInterface
     */
    public function save(CountryInterface $object): CountryInterface;

    /**
     * Get object by id
     *
     * @param int $id
     * @return CountryInterface
     */
    public function getById(int $id): CountryInterface;

    /**
     * Get by Code value
     *
     * @param string $value
     * @return CountryInterface
     */
    public function getByCode(string $value): CountryInterface;

    /**
     * Delete object
     *
     * @param CountryInterface $object
     * @return void
     */
    public function delete(CountryInterface $object): void;

    /**
     * Get a list of object
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return CountrySearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface;
}
