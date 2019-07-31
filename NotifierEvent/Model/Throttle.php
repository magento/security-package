<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\NotifierEvent\Model;

use MSP\NotifierEventApi\Api\Data\RuleInterface;
use MSP\NotifierEventApi\Api\RuleRepositoryInterface;
use MSP\NotifierEventApi\Model\ThrottleInterface;

class Throttle implements ThrottleInterface
{
    /**
     * @var RuleRepositoryInterface
     */
    private $ruleRepository;

    /**
     * Throttle constructor.
     * @param RuleRepositoryInterface $ruleRepository
     */
    public function __construct(
        RuleRepositoryInterface $ruleRepository
    ) {
        $this->ruleRepository = $ruleRepository;
    }

    /**
     * @inheritdoc
     */
    public function execute(RuleInterface $rule): bool
    {
        if (!$rule->getThrottleLimit()) {
            return true;
        }

        $now = time();
        $scoreLimit = $rule->getFireCount() >= $rule->getThrottleLimit();
        $inInterval = $rule->getLastFiredAt() + $rule->getThrottleInterval() > $now;

        if ($scoreLimit && $inInterval) { // Score exceeded within the configured interval
            return false;
        }

        if ($inInterval) {
            $rule->setFireCount($rule->getFireCount() + 1);
        } else {
            $rule->setFireCount(1);
        }

        $rule->setLastFiredAt($now); // Keep updating counter to avoid interval floods

        try {
            $this->ruleRepository->save($rule);
        } catch (\Exception $e) { // Do not stop execution with errors
            return false;
        }

        return true;
    }
}
