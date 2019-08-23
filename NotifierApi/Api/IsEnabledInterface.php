<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierApi\Api;

/**
 * Interface to check if the service is enabled or not
 * @api
 */
interface IsEnabledInterface
{
    /**
     * Return true if module is enabled
     * @return bool
     */
    public function execute(): bool;
}
