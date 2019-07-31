<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\NotifierEvent\Model\Rule\Command;

use Magento\Framework\Exception\CouldNotDeleteException;

/**
 * Delete Rule by ruleId command (Service Provider Interface - SPI)
 *
 * Separate command interface to which Repository proxies initial Delete call, could be considered as SPI - Interfaces
 * that you should extend and implement to customize current behaviour, but NOT expected to be used (called) in the code
 * of business logic directly
 *
 * @see \MSP\NotifierEventApi\Api\RuleRepositoryInterface
 * @api
 */
interface DeleteInterface
{
    /**
     * Delete Rule data by given ruleId
     *
     * @param int $ruleId
     * @return void
     * @throws CouldNotDeleteException
     */
    public function execute(int $ruleId): void;
}
