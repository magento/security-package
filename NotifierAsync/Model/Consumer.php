<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierAsync\Model;

use Magento\NotifierApi\Model\SendMessageInterface;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Consumer for asynchronous messages
 */
class Consumer
{
    /**
     * @var BypassFlag
     */
    private $bypassFlag;

    /**
     * @var SendMessageInterface
     */
    private $sendMessage;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param BypassFlag $bypassFlag
     * @param SerializerInterface $serializer
     * @param SendMessageInterface $sendMessage
     */
    public function __construct(
        BypassFlag $bypassFlag,
        SerializerInterface $serializer,
        SendMessageInterface $sendMessage
    ) {
        $this->bypassFlag = $bypassFlag;
        $this->sendMessage = $sendMessage;
        $this->serializer = $serializer;
    }

    /**
     * @param string $channelCode
     * @param string $message
     * @param string $params
     * @return void
     */
    public function process(string $channelCode, string $message, string $params): void
    {
        $this->bypassFlag->setStatus(true);
        $this->sendMessage->execute($channelCode, $message, $this->serializer->unserialize($params));
        $this->bypassFlag->setStatus(false);
    }
}
