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
     * TODO
     *
     * @param string $channelCode
     * @param string $message
     */
    public function execute(string $channelCode, string $message): void;
}
