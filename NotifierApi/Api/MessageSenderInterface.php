<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\NotifierApi\Api;

use Magento\NotifierApi\Api\Data\AdapterInterface;
use Magento\NotifierApi\Api\Data\MessageInterface;

/**
 * TODO
 *
 * @api
 */
interface MessageSenderInterface
{
    /**
     * Send message to adapter.
     *
     * @param AdapterInterface $adapter
     * @param MessageInterface $message
     */
    public function execute(AdapterInterface $adapter, MessageInterface $message): void;
}
