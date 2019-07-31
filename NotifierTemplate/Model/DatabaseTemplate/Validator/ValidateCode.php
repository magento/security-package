<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\NotifierTemplate\Model\DatabaseTemplate\Validator;

use Magento\Framework\Exception\ValidatorException;
use MSP\NotifierTemplateApi\Api\Data\DatabaseTemplateInterface;
use MSP\NotifierTemplateApi\Model\DatabaseTemplate\Validator\ValidateDatabaseTemplateInterface;

class ValidateCode implements ValidateDatabaseTemplateInterface
{
    /**
     * @inheritDoc
     */
    public function execute(DatabaseTemplateInterface $template): bool
    {
        if (!trim($template->getCode())) {
            throw new ValidatorException(__('Template identifier is required'));
        }

        if (!preg_match('/^(\w+:)?[\w_]+$/', $template->getCode())) {
            throw new ValidatorException(__('Invalid template identifier: Only alphanumeric chars + columns')
            );
        }

        return true;
    }
}
