<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierApi\Model\AdapterEngine;

/**
 * Notifier adapter interface - SPI
 *
 * @api
 */
interface AdapterEngineInterface
{
    /**
     * Execute engine and return true on success. Throw exception on failure.
     * @param string $message
     * @param array $configParams
     * @param array $params
     * @return bool
     */
    public function execute(string $message, array $configParams = [], array $params = []): bool;
}
