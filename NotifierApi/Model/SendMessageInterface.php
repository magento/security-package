<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierApi\Model;

/**
 * Send notifier messages interface
 * @spi
 */
interface SendMessageInterface
{
    /**
     * Send a message, return true, Exception on failure
     * @param string $channelCode
     * @param string $message
     * @param array $params
     * @return bool
     */
    public function execute(string $channelCode, string $message, array $params = []): bool;
}
