<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\NotifierAsync\Plugin;

use Magento\Framework\Exception\NoSuchEntityException;
use MSP\NotifierApi\Api\ChannelRepositoryInterface;
use MSP\NotifierApi\Api\IsEnabledInterface;
use MSP\NotifierApi\Api\SendMessageInterface;
use MSP\NotifierAsync\Model\BypassFlag;
use MSP\NotifierAsync\Model\EnqueueMessage;

class SendMessageAsynchronously
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
     * @param IsEnabledInterface $isEnabled
     * @param ChannelRepositoryInterface $channelRepository
     * @param EnqueueMessage $enqueueMessage
     * @param BypassFlag $bypassFlag
     */
    public function __construct(
        IsEnabledInterface $isEnabled,
        ChannelRepositoryInterface $channelRepository,
        EnqueueMessage $enqueueMessage,
        BypassFlag $bypassFlag
    ) {
        $this->isEnabled = $isEnabled;
        $this->enqueueMessage = $enqueueMessage;
        $this->channelRepository = $channelRepository;
        $this->bypassFlag = $bypassFlag;
    }

    /**
     * @param SendMessageInterface $subject
     * @param callable $proceed
     * @param string $channelCode
     * @param string $message
     * @return bool
     * @throws NoSuchEntityException
     */
    public function aroundExecute(
        SendMessageInterface $subject,
        callable $proceed,
        string $channelCode,
        string $message
    ): bool {
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
            return $proceed($channelCode, $message);
        }

        $this->enqueueMessage->execute($channelCode, $message);
        return true;
    }
}
