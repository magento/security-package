<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\Notifier\Model;

use MSP\NotifierApi\Api\AdaptersPoolInterface;
use MSP\NotifierApi\Api\ChannelRepositoryInterface;
use MSP\NotifierApi\Api\IsEnabledInterface;
use MSP\NotifierApi\Api\SendMessageInterface;
use MSP\NotifierApi\Model\SerializerInterface;

class SendMessage implements SendMessageInterface
{
    /**
     * @var ChannelRepositoryInterface
     */
    private $channelRepository;

    /**
     * @var AdaptersPoolInterface
     */
    private $adapterRepository;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var IsEnabledInterface
     */
    private $isEnabled;

    /**
     * SendMessage constructor.
     * @param ChannelRepositoryInterface $channelRepository
     * @param AdaptersPoolInterface $adapterRepository
     * @param SerializerInterface $serializer
     * @param IsEnabledInterface $isEnabled
     */
    public function __construct(
        ChannelRepositoryInterface $channelRepository,
        AdaptersPoolInterface $adapterRepository,
        SerializerInterface $serializer,
        IsEnabledInterface $isEnabled
    ) {
        $this->channelRepository = $channelRepository;
        $this->adapterRepository = $adapterRepository;
        $this->serializer = $serializer;
        $this->isEnabled = $isEnabled;
    }

    /**
     * @inheritdoc
     */
    public function execute(string $channelCode, string $message): bool
    {
        if (!$this->isEnabled->execute()) {
            return false;
        }

        $channel = $this->channelRepository->getByCode($channelCode);
        if (!$channel->getEnabled()) {
            return false;
        }

        $adapter = $this->adapterRepository->getAdapterByCode($channel->getAdapterCode());
        $params = $this->serializer->unserialize($channel->getConfigurationJson());

        return $adapter->sendMessage($message, $params);
    }
}
