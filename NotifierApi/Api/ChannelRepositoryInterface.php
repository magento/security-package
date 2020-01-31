<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierApi\Api;

/**
 * Channel repository interface
 * @api
 */
interface ChannelRepositoryInterface
{
    /**
     * Save Channel
     * @param \Magento\NotifierApi\Api\Data\ChannelInterface $channel
     * @return int
     */
    public function save(\Magento\NotifierApi\Api\Data\ChannelInterface $channel): int;

    /**
     * Get Channel by id
     * @param int $channelIid
     * @return \Magento\NotifierApi\Api\Data\ChannelInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get(int $channelIid): \Magento\NotifierApi\Api\Data\ChannelInterface;

    /**
     * Get Channel by code
     * @param string $code
     * @return \Magento\NotifierApi\Api\Data\ChannelInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByCode(string $code): \Magento\NotifierApi\Api\Data\ChannelInterface;

    /**
     * Delete Channel
     * @param int $channelId
     * @return void
     */
    public function deleteById(int $channelId): void;

    /**
     * Get a list of Channel
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\NotifierApi\Api\ChannelSearchResultsInterface
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null
    ): \Magento\NotifierApi\Api\ChannelSearchResultsInterface;
}
