<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierEvent\Model;

use Magento\NotifierEventApi\Model\CaptureEventInterface;
use Magento\NotifierEventApi\Model\FireRuleInterface;
use Magento\NotifierEventApi\Model\GetRulesIdsByEventInterface;
use Magento\NotifierEventApi\Model\IsEventExcludedInterface;

class CaptureEvent implements CaptureEventInterface
{
    /**
     * @var GetRulesIdsByEventInterface
     */
    private $getRulesIdsByEvent;

    /**
     * @var FireRuleInterface
     */
    private $fireRule;

    /**
     * @var IsEventExcludedInterface
     */
    private $isEventExcluded;

    /**
     * @param GetRulesIdsByEventInterface $getRulesIdsByEvent
     * @param FireRuleInterface $fireRule
     * @param IsEventExcludedInterface $isEventExcluded
     */
    public function __construct(
        GetRulesIdsByEventInterface $getRulesIdsByEvent,
        FireRuleInterface $fireRule,
        IsEventExcludedInterface $isEventExcluded
    ) {
        $this->getRulesIdsByEvent = $getRulesIdsByEvent;
        $this->fireRule = $fireRule;
        $this->isEventExcluded = $isEventExcluded;
    }

    /**
     * @inheritdoc
     */
    public function execute(string $eventName, array $data = []): bool
    {
        if ($this->isEventExcluded->execute($eventName, $data)) {
            return false;
        }

        $ruleIds = $this->getRulesIdsByEvent->execute($eventName);
        foreach ($ruleIds as $ruleId) {
            $this->fireRule->execute((int) $ruleId, $eventName, $data);
        }

        return true;
    }
}
