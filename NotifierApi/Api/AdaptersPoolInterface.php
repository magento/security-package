<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\NotifierApi\Api;

/**
 * Interface AdapterRepositoryInterface
 * @api
 */
interface AdaptersPoolInterface
{
    /**
     * Get adapters list
     * @return \MSP\NotifierApi\Api\AdapterInterface[]
     */
    public function getAdapters(): array;

    /**
     * Get adapter by code
     * @param string $code
     * @return \MSP\NotifierApi\Api\AdapterInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getAdapterByCode(string $code): \MSP\NotifierApi\Api\AdapterInterface;
}
