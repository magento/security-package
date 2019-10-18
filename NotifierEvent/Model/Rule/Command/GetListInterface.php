<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierEvent\Model\Rule\Command;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\NotifierEventApi\Api\RuleSearchResultsInterface;

/**
 * Find Rule by SearchCriteria command (Service Provider Interface - SPI)
 *
 * Separate command interface to which Repository proxies initial GetList call, could be considered as SPI - Interfaces
 * that you should extend and implement to customize current behaviour, but NOT expected to be used (called) in the code
 * of business logic directly
 *
 * @see \Magento\NotifierEventApi\Api\RuleRepositoryInterface
 * @api
 */
interface GetListInterface
{
    /**
     * Find Rule by given SearchCriteria
     * SearchCriteria is not required because load all sources is useful case
     *
     * @param SearchCriteriaInterface|null $searchCriteria
     * @return RuleSearchResultsInterface
     */
    public function execute(
        SearchCriteriaInterface $searchCriteria = null
    ): RuleSearchResultsInterface;
}
