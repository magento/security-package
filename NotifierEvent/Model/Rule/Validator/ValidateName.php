<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\NotifierEvent\Model\Rule\Validator;

use Magento\Framework\Exception\ValidatorException;
use MSP\NotifierEventApi\Api\Data\RuleInterface;
use MSP\NotifierEventApi\Model\Rule\Validator\ValidateRuleInterface;

class ValidateName implements ValidateRuleInterface
{
    /**
     * @inheritDoc
     */
    public function execute(RuleInterface $rule): bool
    {
        if (!trim($rule->getName())) {
            throw new ValidatorException(__('Rule name is required'));
        }

        return true;
    }
}
