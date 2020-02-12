<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierAsync\Model;

use Magento\Framework\MessageQueue\PublisherInterface;
use Magento\Framework\Serialize\SerializerInterface;

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
     * @param string $channelCode
     * @param string $message
     * @param array $params
     */
    public function execute(string $channelCode, string $message, array $params = []): void
    {
        $this->publisher->publish('magento_notifier.send_message', [
            'channelCode' => $channelCode,
            'message' => $message,
            'params' => $this->serializer->serialize($params)
        ]);
    }
}
