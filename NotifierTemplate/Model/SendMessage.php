<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierTemplate\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Notifier\Model\BuildMessage;
use Magento\NotifierApi\Api\ChannelRepositoryInterface;
use Magento\NotifierTemplateApi\Model\SendMessageInterface;
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
     * @var ChannelRepositoryInterface
     */
    private $channelRepository;

    /**
     * @var BuildMessage
     */
    private $buildMessage;

    /**
     * SendMessage constructor.
     * @param GetMessageTextInterface $getMessageText
     * @param \Magento\NotifierApi\Api\SendMessageInterface $sendMessage
     * @param ChannelRepositoryInterface $channelRepository
     * @param BuildMessage $buildMessage
     */
    public function __construct(
        GetMessageTextInterface $getMessageText,
        \Magento\NotifierApi\Api\SendMessageInterface $sendMessage,
        ChannelRepositoryInterface $channelRepository,
        BuildMessage $buildMessage
    ) {
        $this->getMessageText = $getMessageText;
        $this->sendMessage = $sendMessage;
        $this->channelRepository = $channelRepository;
        $this->buildMessage = $buildMessage;
    }

    /**
     * @inheritDoc
     * @throws NoSuchEntityException
     */
    public function execute(string $channelCode, string $template, array $params = []): void
    {
        $messageText = $this->getMessageText->execute($channelCode, $template, $params);
        $channel = $this->channelRepository->getByCode($channelCode);
        $message = $this->buildMessage->execute($messageText, $params);

        $this->sendMessage->execute($channel, $message);
    }
}
