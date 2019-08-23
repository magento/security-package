<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierApi\Api;

/**
 * Interface AdapterRepositoryInterface
 * @api
 */
interface AdaptersPoolInterface
{
    /**
     * Get adapters list
     * @return \Magento\NotifierApi\Api\AdapterInterface[]
     */
    public function getAdapters(): array;

    /**
     * Get adapter by code
     * @param string $code
     * @return \Magento\NotifierApi\Api\AdapterInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getAdapterByCode(string $code): \Magento\NotifierApi\Api\AdapterInterface;
}
