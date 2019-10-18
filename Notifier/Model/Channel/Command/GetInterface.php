<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Notifier\Model\Channel\Command;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\NotifierApi\Api\Data\ChannelInterface;

/**
 * Get Channel by channelId command (Service Provider Interface - SPI)
 *
 * Separate command interface to which Repository proxies initial Get call, could be considered as SPI - Interfaces
 * that you should extend and implement to customize current behaviour, but NOT expected to be used (called) in the code
 * of business logic directly
 *
 * @see \Magento\NotifierApi\Api\ChannelRepositoryInterface
 * @api
 */
interface GetInterface
{
    /**
     * Get Channel data by given channelId
     *
     * @param int $channelId
     * @return ChannelInterface
     * @throws NoSuchEntityException
     */
    public function execute(int $channelId): ChannelInterface;
}
