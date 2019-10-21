<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierEventBridgeAdapter\Model\AdapterEngine\EventBridge;

use Aws\EventBridge\EventBridgeClient;
use Magento\Framework\Serialize\SerializerInterface;

class ClientRepository
{
    /**
     * @var EventBridgeClient[]
     */
    private $clients = [];

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var ClientFactory
     */
    private $clientFactory;

    /**
     * @param SerializerInterface $serializer
     * @param ClientFactory $clientFactory
     */
    public function __construct(
        SerializerInterface $serializer,
        ClientFactory $clientFactory
    ) {
        $this->serializer = $serializer;
        $this->clientFactory = $clientFactory;
    }

    /**
     * Get an event client bridge
     *
     * @param array $params
     * @return EventBridgeClient
     */
    public function get(array $params): EventBridgeClient
    {
        $key = md5($this->serializer->serialize($params));
        if (!isset($this->clients[$key])) {
            $this->clients[$key] = $this->clientFactory->execute($params);
        }

        return $this->clients[$key];
    }
}
