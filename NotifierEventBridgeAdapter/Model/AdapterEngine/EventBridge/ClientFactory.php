<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierEventBridgeAdapter\Model\AdapterEngine\EventBridge;

use Aws\EventBridge\EventBridgeClient;

class ClientFactory
{
    /**
     * Create a new event bridge client
     *
     * @param array $params
     * @return EventBridgeClient
     */
    public function execute(array $params): EventBridgeClient
    {
        return new EventBridgeClient($params);
    }
}
