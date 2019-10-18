<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierAsync\Model;

use Magento\Framework\MessageQueue\PublisherInterface;

class EnqueueMessage
{
    /**
     * @var PublisherInterface
     */
    private $publisher;

    /**
     * @param PublisherInterface $publisher
     */
    public function __construct(
        PublisherInterface $publisher
    ) {
        $this->publisher = $publisher;
    }

    /**
     * @param string $channelCode
     * @param string $message
     */
    public function execute(string $channelCode, string $message): void
    {
        $this->publisher->publish('msp_notifier.send_message', [
            'channelCode' => $channelCode,
            'message' => $message
        ]);
    }
}
