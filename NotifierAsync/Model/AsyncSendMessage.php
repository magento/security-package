<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\NotifierAsync\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Notifier\Model\SendMessage;
use Magento\NotifierApi\Api\ChannelRepositoryInterface;
use Magento\NotifierApi\Api\IsEnabledInterface;
use Magento\NotifierApi\Model\SendMessageInterface;

class AsyncSendMessage implements SendMessageInterface
{
    /**
     * @var IsEnabledInterface
     */
    private $isEnabled;

    /**
     * @var EnqueueMessage
     */
    private $enqueueMessage;

    /**
     * @var ChannelRepositoryInterface
     */
    private $channelRepository;

    /**
     * @var BypassFlag
     */
    private $bypassFlag;

    /**
     * @var SendMessage
     */
    private $sendMessage;

    /**
     * @param SendMessage $sendMessage
     * @param IsEnabledInterface $isEnabled
     * @param ChannelRepositoryInterface $channelRepository
     * @param EnqueueMessage $enqueueMessage
     * @param BypassFlag $bypassFlag
     */
    public function __construct(
        SendMessage $sendMessage,
        IsEnabledInterface $isEnabled,
        ChannelRepositoryInterface $channelRepository,
        EnqueueMessage $enqueueMessage,
        BypassFlag $bypassFlag
    ) {
        $this->sendMessage = $sendMessage;
        $this->isEnabled = $isEnabled;
        $this->enqueueMessage = $enqueueMessage;
        $this->channelRepository = $channelRepository;
        $this->bypassFlag = $bypassFlag;
    }

    /**
     * @inheritdoc
     */
    public function execute(string $channelCode, string $message, array $params = []): bool
    {
        if (!$this->isEnabled->execute()) {
            return false;
        }

        $channel = $this->channelRepository->getByCode($channelCode);
        if (!$channel->getEnabled()) {
            return false;
        }

        if ($this->bypassFlag->getStatus() ||
            $channel->getExtensionAttributes() === null ||
            !$channel->getExtensionAttributes()->getSendAsync()
        ) {
            return $this->sendMessage($channelCode, $message, $params);
        }

        $this->enqueueMessage->execute($channelCode, $message, $params);
        return true;
    }
}
