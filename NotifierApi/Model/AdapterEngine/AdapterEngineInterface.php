<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierApi\Model\AdapterEngine;

use Magento\NotifierApi\Api\Data\ChannelInterface;
use Magento\NotifierApi\Api\Data\MessageInterface;

/**
 * Notifier adapter engine interface.
 *
 * @api
 */
interface AdapterEngineInterface
{
    /**
     *  Notifier adapter engine interface.
     *
     * Throws exception on failure.
     *
     * @param ChannelInterface $channel
     * @param MessageInterface $message
     */
    public function execute(ChannelInterface $channel, MessageInterface $message): void;
}
