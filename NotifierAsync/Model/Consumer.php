<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierAsync\Model;

use Magento\NotifierApi\Api\SendMessageInterface;

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
     * @param array $params
     */
    public function process(string $channelCode, string $message, array $params): void
    {
        $this->bypassFlag->setStatus(true);
        $this->sendMessage->execute($channelCode, $message, $params);
        $this->bypassFlag->setStatus(false);
    }
}
