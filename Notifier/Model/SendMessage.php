<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Notifier\Model;

use Magento\NotifierApi\Api\AdapterPoolInterface;
use Magento\NotifierApi\Api\ChannelRepositoryInterface;
use Magento\NotifierApi\Api\Data\MessageInterfaceFactory;
use Magento\NotifierApi\Api\IsEnabledInterface;
use Magento\NotifierApi\Api\MessageSenderInterface;
use Magento\NotifierApi\Exception\NotifierChannelDisabledException;
use Magento\NotifierApi\Exception\NotifierDisabledException;
use Magento\NotifierApi\Model\SendMessageInterface;
use Magento\NotifierApi\Model\SerializerInterface;

class SendMessage implements SendMessageInterface
{
    /**
     * @var ChannelRepositoryInterface
     */
    private $channelRepository;

    /**
     * @var AdapterPoolInterface
     */
    private $adaptersPool;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var IsEnabledInterface
     */
    private $isEnabled;

    /**
     * @var MessageSenderInterface
     */
    private $messageSender;

    /**
     * @var MessageInterfaceFactory
     */
    private $messageFactory;

    /**
     * SendMessage constructor.
     * @param ChannelRepositoryInterface $channelRepository
     * @param AdapterPoolInterface $adaptersPool
     * @param SerializerInterface $serializer
     * @param IsEnabledInterface $isEnabled
     * @param MessageSenderInterface $messageSender
     * @param MessageInterfaceFactory $messageFactory
     */
    public function __construct(
        ChannelRepositoryInterface $channelRepository,
        AdapterPoolInterface $adaptersPool,
        SerializerInterface $serializer,
        IsEnabledInterface $isEnabled,
        MessageSenderInterface $messageSender,
        MessageInterfaceFactory $messageFactory
    ) {
        $this->channelRepository = $channelRepository;
        $this->adaptersPool = $adaptersPool;
        $this->serializer = $serializer;
        $this->isEnabled = $isEnabled;
        $this->messageSender = $messageSender;
        $this->messageFactory = $messageFactory;
    }

    /**
     * @inheritdoc
     */
    public function execute(string $channelCode, string $message): void
    {
        if (!$this->isEnabled->execute()) {
            throw new NotifierDisabledException(__('Notifier service is disabled.'));
        }

        $channel = $this->channelRepository->getByCode($channelCode);
        if (!$channel->getEnabled()) {
            throw new NotifierChannelDisabledException(__('Notifier channel ' . $channelCode . ' is disabled.'));
        }

        $adapter = $this->adaptersPool->getAdapterByCode($channel->getAdapterCode());
        $configParams = $this->serializer->unserialize($channel->getConfigurationJson());

        $notificationMessage = $this->messageFactory->create(
            ['message' => $message, 'params' => $configParams]
        );

        $this->messageSender->execute($adapter, $notificationMessage);
    }
}
