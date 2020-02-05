<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Notifier\Model;

use Magento\NotifierApi\Api\AdapterEnginePoolInterface;
use Magento\NotifierApi\Api\AdapterValidatorPoolInterface;
use Magento\NotifierApi\Api\Data\ChannelInterface;
use Magento\NotifierApi\Api\Data\MessageInterface;
use Magento\NotifierApi\Api\GetChannelConfigurationInterface;
use Magento\NotifierApi\Api\IsEnabledInterface;
use Magento\NotifierApi\Exception\NotifierChannelDisabledException;
use Magento\NotifierApi\Exception\NotifierDisabledException;
use Magento\NotifierApi\Api\SendMessageInterface;

/**
 * @inheritdoc
 */
class SendMessage implements SendMessageInterface
{
    /**
     * @var IsEnabledInterface
     */
    private $isEnabled;

    /**
     * @var AdapterEnginePoolInterface
     */
    private $adapterEnginePool;

    /**
     * @var AdapterValidatorPoolInterface
     */
    private $adapterValidatorPool;

    /**
     * @var GetChannelConfigurationInterface
     */
    private $getChannelConfiguration;

    /**
     * @param AdapterEnginePoolInterface $adapterEnginePool
     * @param AdapterValidatorPoolInterface $adapterValidatorPool
     * @param IsEnabledInterface $isEnabled
     * @param GetChannelConfigurationInterface $getChannelConfiguration
     */
    public function __construct(
        AdapterEnginePoolInterface $adapterEnginePool,
        AdapterValidatorPoolInterface $adapterValidatorPool,
        IsEnabledInterface $isEnabled,
        GetChannelConfigurationInterface $getChannelConfiguration
    ) {
        $this->adapterEnginePool = $adapterEnginePool;
        $this->adapterValidatorPool = $adapterValidatorPool;
        $this->isEnabled = $isEnabled;
        $this->getChannelConfiguration = $getChannelConfiguration;
    }

    /**
     * @inheritdoc
     */
    public function execute(ChannelInterface $channel, MessageInterface $notificationMessage): void
    {
        if (!$this->isEnabled->execute()) {
            throw new NotifierDisabledException(__('Notifier service is disabled.'));
        }

        if (!$channel->getEnabled()) {
            throw new NotifierChannelDisabledException(__('Notifier channel ' . $channel->getCode() . ' is disabled.'));
        }

        $this->validate($channel, $notificationMessage);
        $this->sendMessage($channel, $notificationMessage);
    }

    /**
     * Validate message text and configuration.
     *
     * @param ChannelInterface $channel
     * @param MessageInterface $message
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\ValidatorException
     */
    private function validate(ChannelInterface $channel, MessageInterface $message): void
    {
        $adapterCode = $channel->getAdapterCode();
        $validator = $this->adapterValidatorPool->getAdapterValidatorByCode($adapterCode);

        $validator->validateMessage($message->getMessage());
        $validator->validateParams($this->getChannelConfiguration->execute($channel));
    }

    /**
     * Send message by adapter engine.
     *
     * @param ChannelInterface $channel
     * @param MessageInterface $message
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function sendMessage(ChannelInterface $channel, MessageInterface $message): void
    {
        $adapterCode = $channel->getAdapterCode();
        $engine = $this->adapterEnginePool->getAdapterEngineByCode($adapterCode);

        $engine->execute($channel, $message);
    }
}
