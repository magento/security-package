<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\Notifier\Model\Channel\Validator;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\ValidatorException;
use MSP\NotifierApi\Api\AdaptersPoolInterface;
use MSP\NotifierApi\Api\Data\ChannelInterface;
use MSP\NotifierApi\Model\Channel\Validator\ValidateChannelInterface;
use MSP\NotifierApi\Model\SerializerInterface;

class ValidateAdapter implements ValidateChannelInterface
{
    /**
     * @var AdaptersPoolInterface
     */
    private $adaptersPool;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * BasicValidator constructor.
     * @param AdaptersPoolInterface $adaptersPool
     * @param SerializerInterface $serializer
     */
    public function __construct(
        AdaptersPoolInterface $adaptersPool,
        SerializerInterface $serializer
    ) {
        $this->adaptersPool = $adaptersPool;
        $this->serializer = $serializer;
    }

    /**
     * @inheritDoc
     */
    public function execute(ChannelInterface $channel): void
    {
        try {
            $adapter = $this->adaptersPool->getAdapterByCode($channel->getAdapterCode());
        } catch (NoSuchEntityException $e) {
            throw new ValidatorException(__('Invalid adapter code'));
        }

        // Validate adapter's specific configuration
        $params = $this->serializer->unserialize($channel->getConfigurationJson());
        $adapter->validateParams($params);
    }
}
