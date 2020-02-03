<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\NotifierApi\Api;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\NotifierApi\Model\AdapterEngine\AdapterValidatorInterface;

/**
 * TODO
 *
 * @api
 */
interface AdapterValidatorPoolInterface
{
    /**
     * Get adapter validator list.
     *
     * @return AdapterValidatorInterface[]
     */
    public function getAdapterValidators(): array;

    /**
     * Get adapter validator by code.
     *
     * @param string $code
     * @return AdapterValidatorInterface
     * @throws NoSuchEntityException
     */
    public function getAdapterValidatorByCode(string $code): AdapterValidatorInterface;
}
