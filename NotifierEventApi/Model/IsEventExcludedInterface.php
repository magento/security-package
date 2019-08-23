<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierEventApi\Model;

/**
 * Event exclusion interface (Service Provider Interface - SPI)
 *
 * @api
 */
interface IsEventExcludedInterface
{
    /**
     * Return true if event should be excluded
     *
     * @param string $eventName
     * @param array $data
     * @return bool
     */
    public function execute(string $eventName, array $data = []): bool;
}
