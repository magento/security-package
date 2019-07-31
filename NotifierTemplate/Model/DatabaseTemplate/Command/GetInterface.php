<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\NotifierTemplate\Model\DatabaseTemplate\Command;

use Magento\Framework\Exception\NoSuchEntityException;
use MSP\NotifierTemplateApi\Api\Data\DatabaseTemplateInterface;

/**
 * Get DatabaseTemplate by databaseTemplateId command (Service Provider Interface - SPI)
 *
 * Separate command interface to which Repository proxies initial Get call, could be considered as SPI - Interfaces
 * that you should extend and implement to customize current behaviour, but NOT expected to be used (called) in the code
 * of business logic directly
 *
 * @see \MSP\NotifierTemplateApi\Api\DatabaseTemplateRepositoryInterface
 * @api
 */
interface GetInterface
{
    /**
     * Get DatabaseTemplate data by given databaseTemplateId
     *
     * @param int $databaseTemplateId
     * @return DatabaseTemplateInterface
     * @throws NoSuchEntityException
     */
    public function execute(int $databaseTemplateId): DatabaseTemplateInterface;
}
