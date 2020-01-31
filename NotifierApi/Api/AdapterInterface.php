<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierApi\Api;

/**
 * Interface AdapterInterface
 * @api
 */
interface AdapterInterface
{
    /**
     * Get adapter code
     * @return string
     */
    public function getCode(): string;

    /**
     * Get adapter description
     * @return string
     */
    public function getDescription(): string;



    /**
     * Send message to adapter. Return true on success, throws exception on failure.
     * @param string $message
     * @param array $configParams
     * @param array $params
     * @return bool
     */
    public function sendMessage(string $message, array $configParams = [], array $params = []): bool;
}
