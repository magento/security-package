<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\NotifierEventApi\Model\Rule\Validator;

use Magento\Framework\Exception\ValidatorException;
use MSP\NotifierEventApi\Api\Data\RuleInterface;

/**
 * Rule validator - SPI
 *
 * @api
 */
interface ValidateRuleInterface
{
    /**
     * Execute validation. Return true on success or trigger an exception on failure
     * @param RuleInterface $rule
     * @return bool
     * @throws ValidatorException
     */
    public function execute(RuleInterface $rule): bool;
}
