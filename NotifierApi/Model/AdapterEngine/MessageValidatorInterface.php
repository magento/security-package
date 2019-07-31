<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\NotifierApi\Model\AdapterEngine;

use Magento\Framework\Exception\ValidatorException;

/**
 * Validates the content of a notifier message - SPI
 *
 * @api
 */
interface MessageValidatorInterface
{
    /**
     * Must:
     *  - Throw an ValidationException in case of failure
     *  - Return true on success
     *
     * @param string $message
     * @return bool
     * @throws ValidatorException
     */
    public function execute(string $message): bool;
}
