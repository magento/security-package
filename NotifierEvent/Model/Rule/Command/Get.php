<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\NotifierEvent\Model\Rule\Command;

use Magento\Framework\Exception\NoSuchEntityException;
use MSP\NotifierEvent\Model\ResourceModel\Rule;
use MSP\NotifierEventApi\Api\Data\RuleInterface;
use MSP\NotifierEventApi\Api\Data\RuleInterfaceFactory;

/**
 * @inheritdoc
 */
class Get implements GetInterface
{
    /**
     * @var Rule
     */
    private $resource;

    /**
     * @var RuleInterfaceFactory
     */
    private $factory;

    /**
     * @param Rule $resource
     * @param RuleInterfaceFactory $factory
     */
    public function __construct(
        Rule $resource,
        RuleInterfaceFactory $factory
    ) {
        $this->resource = $resource;
        $this->factory = $factory;
    }

    /**
     * @inheritdoc
     */
    public function execute(int $ruleId): RuleInterface
    {
        /** @var RuleInterface $rule */
        $rule = $this->factory->create();
        $this->resource->load(
            $rule,
            $ruleId,
            'rule_id'
        );

        if (null === $rule->getId()) {
            throw new NoSuchEntityException(__('Rule with id "%value" does not exist.', [
                'value' => $ruleId
            ]));
        }

        return $rule;
    }
}
