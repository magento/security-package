<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierEventApi\Model;

/**
 * Fire a rule (Service Provider Interface - SPI)
 *
 * @api
 */
interface FireRuleInterface
{
    /**
     * @param int $ruleId
     * @param string $eventName
     * @param array $data
     * @return void
     */
    public function execute(int $ruleId, string $eventName, array $data): void;
}
