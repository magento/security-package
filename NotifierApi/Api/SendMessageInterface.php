<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\NotifierApi\Api;

/**
 * Send notifier messages interface
 * @api
 */
interface SendMessageInterface
{
    /**
     * Send a message, return true, Exception on failure
     * @param string $channelCode
     * @param string $message
     * @return bool
     * @return \Magento\Framework\Exception\ValidatorException
     */
    public function execute(string $channelCode, string $message): bool;
}
