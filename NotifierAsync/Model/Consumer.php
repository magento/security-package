<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierAsync\Model;

use Magento\Notifier\Model\BuildMessage;
use Magento\NotifierApi\Api\ChannelRepositoryInterface;
use Magento\NotifierApi\Api\SendMessageInterface;
use Magento\NotifierApi\Model\SerializerInterface;
use Magento\NotifierApi\Api\Data\MessageInterfaceFactory;


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
     * @var ChannelRepositoryInterface
     */
    private $channelRepository;
    /**
     * @var BuildMessage
     */
    private $buildMessage;

    /**
     * @param BypassFlag $bypassFlag
     * @param SerializerInterface $serializer
     * @param SendMessageInterface $sendMessage
     * @param ChannelRepositoryInterface $channelRepository
     * @param BuildMessage $buildMessage
     */
    public function __construct(
        BypassFlag $bypassFlag,
        SerializerInterface $serializer,
        SendMessageInterface $sendMessage,
        ChannelRepositoryInterface $channelRepository,
        BuildMessage $buildMessage
    ) {
        $this->bypassFlag = $bypassFlag;
        $this->sendMessage = $sendMessage;
        $this->serializer = $serializer;
        $this->channelRepository = $channelRepository;
        $this->buildMessage = $buildMessage;
    }

    /**
     * @param string $channelCode
     * @param string $messageText
     * @param string $params
     * @return void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function process(string $channelCode, string $messageText, string $params): void
    {
        $this->bypassFlag->setStatus(true);
        $channel = $this->channelRepository->getByCode($channelCode);
        $message = $this->buildMessage->execute($messageText, $this->serializer->unserialize($params));
        $this->sendMessage->execute($channel, $message);
        $this->bypassFlag->setStatus(false);
    }
}
