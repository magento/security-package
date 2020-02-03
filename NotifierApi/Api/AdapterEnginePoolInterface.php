<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\NotifierApi\Api;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\NotifierApi\Model\AdapterEngine\AdapterEngineInterface;

/**
 * TODO
 *
 * @api
 */
interface AdapterEnginePoolInterface
{
    /**
     * Get adapter engine list.
     *
     * @return AdapterEngineInterface[]
     */
    public function getAdapterEngines(): array;

    /**
     * Get adapter engine by code.
     *
     * @param string $code
     * @return AdapterEngineInterface
     * @throws NoSuchEntityException
     */
    public function getAdapterEngineByCode(string $code): AdapterEngineInterface;
}
