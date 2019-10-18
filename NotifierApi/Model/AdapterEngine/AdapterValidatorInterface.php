<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierApi\Model\AdapterEngine;

use Magento\Framework\Exception\ValidatorException;

/**
 * Interface for adapter information validation - SPI
 *
 * @api
 */
interface AdapterValidatorInterface
{
    /**
     * Validate a message
     * @param string $message
     * @return bool
     * @throws ValidatorException
     */
    public function validateMessage(string $message): bool;

    /**
     * Validate parameters
     * @param array $params
     * @return bool
     * @throws ValidatorException
     */
    public function validateParams(array $params): bool;
}
