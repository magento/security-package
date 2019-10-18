<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierTemplate\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\NotifierTemplateApi\Api\SendMessageInterface;
use Magento\NotifierTemplateApi\Model\GetMessageTextInterface;

class SendMessage implements SendMessageInterface
{
    /**
     * @var GetMessageTextInterface
     */
    private $getMessageText;

    /**
     * @var \Magento\NotifierApi\Api\SendMessageInterface
     */
    private $sendMessage;

    /**
     * SendMessage constructor.
     * @param GetMessageTextInterface $getMessageText
     * @param \Magento\NotifierApi\Api\SendMessageInterface $sendMessage
     */
    public function __construct(
        GetMessageTextInterface $getMessageText,
        \Magento\NotifierApi\Api\SendMessageInterface $sendMessage
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
