<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierAsync\Plugin;

use Magento\NotifierApi\Api\ChannelRepositoryInterface;
use Magento\NotifierApi\Api\Data\ChannelInterface;
use Magento\NotifierApi\Api\Data\MessageInterface;
use Magento\NotifierApi\Api\IsEnabledInterface;
use Magento\NotifierApi\Exception\NotifierChannelDisabledException;
use Magento\NotifierApi\Exception\NotifierDisabledException;
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
     * @param ChannelInterface $channel
     * @param MessageInterface $notificationMessage
     * @throws NotifierChannelDisabledException
     * @throws NotifierDisabledException
     */
    public function aroundExecute(
        SendMessageInterface $subject,
        callable $proceed,
        ChannelInterface $channel,
        MessageInterface $notificationMessage
    ): void {
        if (!$this->isEnabled->execute()) {
            throw new NotifierDisabledException(__('Notifier service is disabled.'));
        }

        if (!$channel->getEnabled()) {
            throw new NotifierChannelDisabledException(__('Notifier channel ' . $channel->getCode() . ' is disabled.'));
        }

        if ($this->bypassFlag->getStatus() ||
            $channel->getExtensionAttributes() === null ||
            !$channel->getExtensionAttributes()->getSendAsync()
        ) {
            $proceed($channel, $notificationMessage);
        }

        $this->enqueueMessage->execute($channel, $notificationMessage);
    }
}
