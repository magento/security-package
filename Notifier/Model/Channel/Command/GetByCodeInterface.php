<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\Notifier\Model\Channel\Command;

use Magento\Framework\Exception\NoSuchEntityException;
use MSP\NotifierApi\Api\Data\ChannelInterface;

/**
 * Get Channel by code command (Service Provider Interface - SPI)
 *
 * Separate command interface to which Repository proxies initial Get call, could be considered as SPI - Interfaces
 * that you should extend and implement to customize current behaviour, but NOT expected to be used (called) in the code
 * of business logic directly
 *
 * @see \MSP\NotifierApi\Api\ChannelRepositoryInterface
 * @api
 */
interface GetByCodeInterface
{
    /**
     * Get Channel data by given code
     *
     * @param string $code
     * @return ChannelInterface
     * @throws NoSuchEntityException
     */
    public function execute(string $code): ChannelInterface;
}
