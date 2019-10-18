<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Notifier\Model\Channel\Command;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\NotifierApi\Api\ChannelSearchResultsInterface;

/**
 * Find Channel by SearchCriteria command (Service Provider Interface - SPI)
 *
 * Separate command interface to which Repository proxies initial GetList call, could be considered as SPI - Interfaces
 * that you should extend and implement to customize current behaviour, but NOT expected to be used (called) in the code
 * of business logic directly
 *
 * @see \Magento\NotifierApi\Api\ChannelRepositoryInterface
 * @api
 */
interface GetListInterface
{
    /**
     * Find Channel by given SearchCriteria
     * SearchCriteria is not required because load all sources is useful case
     *
     * @param SearchCriteriaInterface|null $searchCriteria
     * @return ChannelSearchResultsInterface
     */
    public function execute(
        SearchCriteriaInterface $searchCriteria = null
    ): ChannelSearchResultsInterface;
}
