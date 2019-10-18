<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierTemplate\Model\DatabaseTemplate\Command;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\NotifierTemplateApi\Api\Data\DatabaseTemplateInterface;

/**
 * Save DatabaseTemplate data command (Service Provider Interface - SPI)
 *
 * Separate command interface to which Repository proxies initial Save call, could be considered as SPI - Interfaces
 * that you should extend and implement to customize current behaviour, but NOT expected to be used (called) in the code
 * of business logic directly
 *
 * @see \Magento\NotifierTemplateApi\Api\DatabaseTemplateRepositoryInterface
 * @api
 */
interface SaveInterface
{
    /**
     * Save DatabaseTemplate data
     *
     * @param DatabaseTemplateInterface $source
     * @return int
     * @throws CouldNotSaveException
     */
    public function execute(DatabaseTemplateInterface $source): int;
}
