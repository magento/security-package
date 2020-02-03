<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\NotifierApi\Model;

use Magento\NotifierApi\Api\AdapterEnginePoolInterface;
use Magento\NotifierApi\Api\AdapterValidatorPoolInterface;
use Magento\NotifierApi\Api\Data\AdapterInterface;
use Magento\NotifierApi\Api\Data\MessageInterface;
use Magento\NotifierApi\Api\MessageSenderInterface;

/**
 * TODO
 */
class MessageSender implements MessageSenderInterface
{
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
        AdapterValidatorPoolInterface $adapterValidatorPool
    ) {
        $this->adapterEnginePool = $adapterEnginePool;
        $this->adapterValidatorPool = $adapterValidatorPool;
    }

    /**
     * TODO
     *
     * @param AdapterInterface $adapter
     * @param MessageInterface $message
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\ValidatorException
     */
    public function execute(AdapterInterface $adapter, MessageInterface $message): void
    {
        $adapterCode = $adapter->getCode();
        $validator = $this->adapterValidatorPool->getAdapterValidatorByCode($adapterCode);
        $engine = $this->adapterEnginePool->getAdapterEngineByCode($adapterCode);

        $validator->validateMessage($message->getMessage());
        $validator->validateParams($message->getParams());

        $engine->execute($message);
    }
}
