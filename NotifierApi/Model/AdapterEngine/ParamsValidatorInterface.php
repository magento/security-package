<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierApi\Model\AdapterEngine;

use Magento\Framework\Exception\ValidatorException;

/**
 * Validates parameters for a notifier channel - SPI
 *
 * @api
 */
interface ParamsValidatorInterface
{
    /**
     * Must:
     *  - Throw an InvalidArgumentException in case of failure
     *  - Return true on success
     *
     * @param array $params
     * @return bool
     * @throws ValidatorException
     */
    public function execute(array $params): bool;
}
