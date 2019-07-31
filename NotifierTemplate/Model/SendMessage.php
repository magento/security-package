<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\NotifierTemplate\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use MSP\NotifierTemplateApi\Api\SendMessageInterface;
use MSP\NotifierTemplateApi\Model\GetMessageTextInterface;

class SendMessage implements SendMessageInterface
{
    /**
     * @var GetMessageTextInterface
     */
    private $getMessageText;

    /**
     * @var \MSP\NotifierApi\Api\SendMessageInterface
     */
    private $sendMessage;

    /**
     * SendMessage constructor.
     * @param GetMessageTextInterface $getMessageText
     * @param \MSP\NotifierApi\Api\SendMessageInterface $sendMessage
     */
    public function __construct(
        GetMessageTextInterface $getMessageText,
        \MSP\NotifierApi\Api\SendMessageInterface $sendMessage
    ) {
        $this->getMessageText = $getMessageText;
        $this->sendMessage = $sendMessage;
    }

    /**
     * @inheritDoc
     * @throws NoSuchEntityException
     */
    public function execute(string $channelCode, string $template, array $params = []): bool
    {
        $message = $this->getMessageText->execute($channelCode, $template, $params);

        return $this->sendMessage->execute($channelCode, $message);
    }
}
