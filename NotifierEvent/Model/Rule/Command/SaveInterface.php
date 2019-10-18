<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierEvent\Model\Rule\Command;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\NotifierEventApi\Api\Data\RuleInterface;

/**
 * Save Rule data command (Service Provider Interface - SPI)
 *
 * Separate command interface to which Repository proxies initial Save call, could be considered as SPI - Interfaces
 * that you should extend and implement to customize current behaviour, but NOT expected to be used (called) in the code
 * of business logic directly
 *
 * @see \Magento\NotifierEventApi\Api\RuleRepositoryInterface
 * @api
 */
interface SaveInterface
{
    /**
     * Save Rule data
     *
     * @param RuleInterface $source
     * @return int
     * @throws CouldNotSaveException
     */
    public function execute(RuleInterface $source): int;
}
