<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierTemplate\Model\DatabaseTemplate\Command;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\NotifierTemplateApi\Api\Data\DatabaseTemplateInterface;

/**
 * Get DatabaseTemplate by adapterCode command (Service Provider Interface - SPI)
 *
 * Separate command interface to which Repository proxies initial Get call, could be considered as SPI - Interfaces
 * that you should extend and implement to customize current behaviour, but NOT expected to be used (called) in the code
 * of business logic directly
 *
 * @see \Magento\NotifierTemplateApi\Api\DatabaseTemplateRepositoryInterface
 * @api
 */
interface GetByAdapterCodeInterface
{
    /**
     * Get DatabaseTemplate data by given adapterCode
     *
     * @param string $adapterCode
     * @return DatabaseTemplateInterface
     * @throws NoSuchEntityException
     */
    public function execute(string $adapterCode): DatabaseTemplateInterface;
}
