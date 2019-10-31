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
