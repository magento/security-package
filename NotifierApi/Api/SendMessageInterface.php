<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierApi\Api;

use Magento\NotifierApi\Api\Data\ChannelInterface;
use Magento\NotifierApi\Api\Data\MessageInterface;

/**
 * Send notifier messages interface
 * @spi
 */
interface SendMessageInterface
{
    /**
     * TODO
     *
     * @param ChannelInterface $channel
     * @param MessageInterface $notificationMessage
     */
    public function execute(ChannelInterface $channel, MessageInterface $notificationMessage): void;
}
