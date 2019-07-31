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

class ValidateThrottle implements ValidateRuleInterface
{
    /**
     * @inheritDoc
     */
    public function execute(RuleInterface $rule): bool
    {
        if ($rule->getThrottleInterval() < 0) {
            throw new ValidatorException(__('Throttle interval must be greater or equal to 0'));
        }

        if ($rule->getThrottleLimit() < 0) {
            throw new ValidatorException(__('Throttle limit must be greater or equal to 0'));
        }

        return true;
    }
}
