<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierEvent\Model\Rule\Validator;

use Magento\Framework\Exception\ValidatorException;
use Magento\NotifierEventApi\Api\Data\RuleInterface;
use Magento\NotifierEventApi\Model\Rule\Validator\ValidateRuleInterface;

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
