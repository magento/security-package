<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\NotifierAsync\Model;

use MSP\NotifierApi\Api\SendMessageInterface;

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
     * @param BypassFlag $bypassFlag
     * @param SendMessageInterface $sendMessage
     */
    public function __construct(
        BypassFlag $bypassFlag,
        SendMessageInterface $sendMessage
    ) {
        $this->bypassFlag = $bypassFlag;
        $this->sendMessage = $sendMessage;
    }

    /**
     * @param string $channelCode
     * @param string $message
     */
    public function process(string $channelCode, string $message): void
    {
        $this->bypassFlag->setStatus(true);
        $this->sendMessage->execute($channelCode, $message);
        $this->bypassFlag->setStatus(false);
    }
}
