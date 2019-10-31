<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierEventApi\Model;

use Magento\NotifierEventApi\Api\Data\RuleInterface;

/**
 * Throttle manager (Service Provider Interface - SPI)
 *
 * @api
 */
interface ThrottleInterface
{
    /**
     * Update throttle information and true if rule exceeded the throttle quota
     * @param RuleInterface $rule
     * @return bool
     */
    public function execute(RuleInterface $rule): bool;
}
