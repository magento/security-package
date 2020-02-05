<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierAsync\Model;

use Magento\Framework\MessageQueue\PublisherInterface;
use Magento\NotifierApi\Api\Data\ChannelInterface;
use Magento\NotifierApi\Api\Data\MessageInterface;
use Magento\NotifierApi\Model\SerializerInterface;

class EnqueueMessage
{
    /**
     * @var PublisherInterface
     */
    private $publisher;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param PublisherInterface $publisher
     * @param SerializerInterface $serializer
     */
    public function __construct(
        PublisherInterface $publisher,
        SerializerInterface $serializer
    ) {
        $this->publisher = $publisher;
        $this->serializer = $serializer;
    }

    /**
     * @param ChannelInterface $channel
     * @param MessageInterface $notificationMessage
     */
    public function execute(ChannelInterface $channel, MessageInterface $notificationMessage): void
    {
        $this->publisher->publish('magento_notifier.send_message', [
            'channelCode' => $channel->getCode(),
            'messageText' => $notificationMessage->getMessage(),
            'params' => $this->serializer->serialize($notificationMessage->getParams())
        ]);
    }
}
