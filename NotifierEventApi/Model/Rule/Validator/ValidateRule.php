<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierEventApi\Model\Rule\Validator;

use Magento\NotifierEventApi\Api\Data\RuleInterface;

/**
 * @api
 */
class ValidateRule implements ValidateRuleInterface
{
    /**
     * @var ValidateRuleInterface[]
     */
    private $validators;

    /**
     * @param ValidateRuleInterface[] $validators
     */
    public function __construct(
        array $validators = []
    ) {
        $this->validators = $validators;

        foreach ($this->validators as $k => $validator) {
            if (!($validator instanceof ValidateRuleInterface)) {
                throw new \InvalidArgumentException('Validator %1 must implement ValidateRuleInterface', $k);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function execute(RuleInterface $rule): bool
    {
        foreach ($this->validators as $validator) {
            $validator->execute($rule);
        }

        return true;
    }
}
