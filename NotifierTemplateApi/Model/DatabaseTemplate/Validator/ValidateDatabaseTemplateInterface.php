<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\NotifierTemplateApi\Model\DatabaseTemplate\Validator;

use Magento\Framework\Exception\ValidatorException;
use MSP\NotifierTemplateApi\Api\Data\DatabaseTemplateInterface;

/**
 * Interface DatabaseTemplateValidatorInterface - SPI
 *
 * @api
 */
interface ValidateDatabaseTemplateInterface
{
    /**
     * Execute validation. Return true on success or trigger an exception on failure
     * @param DatabaseTemplateInterface $template
     * @return bool
     * @throws ValidatorException
     */
    public function execute(DatabaseTemplateInterface $template): bool;
}
