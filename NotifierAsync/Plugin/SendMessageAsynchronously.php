<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierAsync\Plugin;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\NotifierApi\Api\ChannelRepositoryInterface;
use Magento\NotifierApi\Api\IsEnabledInterface;
use Magento\NotifierApi\Api\SendMessageInterface;
use Magento\NotifierAsync\Model\BypassFlag;
use Magento\NotifierAsync\Model\EnqueueMessage;

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
