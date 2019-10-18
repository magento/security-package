<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierEvent\Model\Rule\Command;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\NotifierEventApi\Api\Data\RuleInterface;

/**
 * Get Rule by ruleId command (Service Provider Interface - SPI)
 *
 * Separate command interface to which Repository proxies initial Get call, could be considered as SPI - Interfaces
 * that you should extend and implement to customize current behaviour, but NOT expected to be used (called) in the code
 * of business logic directly
 *
 * @see \Magento\NotifierEventApi\Api\RuleRepositoryInterface
 * @api
 */
interface GetInterface
{
    /**
     * Get Rule data by given ruleId
     *
     * @param int $ruleId
     * @return RuleInterface
     * @throws NoSuchEntityException
     */
    public function execute(int $ruleId): RuleInterface;
}
