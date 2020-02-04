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
use Magento\NotifierApi\Api\IsEnabledInterface;
use Magento\NotifierApi\Exception\NotifierChannelDisabledException;
use Magento\NotifierApi\Exception\NotifierDisabledException;
use Magento\NotifierApi\Api\SendMessageInterface;

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
     * @param AdapterEnginePoolInterface $adapterEnginePool
     * @param AdapterValidatorPoolInterface $adapterValidatorPool
     */
    public function __construct(
        AdapterEnginePoolInterface $adapterEnginePool,
        AdapterValidatorPoolInterface $adapterValidatorPool,
        IsEnabledInterface $isEnabled
    ) {
        $this->adapterEnginePool = $adapterEnginePool;
        $this->adapterValidatorPool = $adapterValidatorPool;
        $this->isEnabled = $isEnabled;
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

        $this->validateMessage($channel, $notificationMessage);
        $this->sendMessage($channel, $notificationMessage);
    }

    /**
     * TODO
     *
     * @param ChannelInterface $channel
     * @param MessageInterface $message
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\ValidatorException
     */
    private function validateMessage(ChannelInterface $channel, MessageInterface $message): void
    {
        $adapterCode = $channel->getAdapterCode();
        $validator = $this->adapterValidatorPool->getAdapterValidatorByCode($adapterCode);

        $validator->validateMessage($message->getMessage());
        $validator->validateParams($message->getParams());
    }

    /**
     * TODO
     *
     * @param ChannelInterface $channel
     * @param MessageInterface $message
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function sendMessage(ChannelInterface $channel, MessageInterface $message): void
    {
        $adapterCode = $channel->getAdapterCode();
        $engine = $this->adapterEnginePool->getAdapterEngineByCode($adapterCode);

        $engine->execute($message);
    }
}
