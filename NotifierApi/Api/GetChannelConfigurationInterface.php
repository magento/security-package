<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierApi\Api;


use Magento\NotifierApi\Api\Data\ChannelInterface;

/**
 * Get unserialized channel configuration.
 *
 * @api
 */
interface GetChannelConfigurationInterface
{
    /**
     * Get unserialized channel configuration.
     *
     * @param ChannelInterface $channel
     * @return array
     */
    public function execute(ChannelInterface $channel): array;
}
