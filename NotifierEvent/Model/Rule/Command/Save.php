<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierEvent\Model\Rule\Command;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\NotifierEvent\Model\ResourceModel\Rule;
use Magento\NotifierEventApi\Api\Data\RuleInterface;
use Magento\NotifierEventApi\Model\Rule\Validator\ValidateRuleInterface;
use Psr\Log\LoggerInterface;

/**
 * @inheritdoc
 */
class Save implements SaveInterface
{
    /**
     * @var Rule
     */
    private $resource;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ValidateRuleInterface
     */
    private $validateRule;

    /**
     * @param Rule $resource
     * @param ValidateRuleInterface $validateRule
     * @param LoggerInterface $logger
     */
    public function __construct(
        Rule $resource,
        ValidateRuleInterface $validateRule,
        LoggerInterface $logger
    ) {
        $this->resource = $resource;
        $this->logger = $logger;
        $this->validateRule = $validateRule;
    }

    /**
     * @inheritdoc
     */
    public function execute(RuleInterface $rule): int
    {
        $this->validateRule->execute($rule);

        try {
            $this->resource->save($rule);
            return (int) $rule->getId();
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new CouldNotSaveException(__('Could not save Rule'), $e);
        }
    }
}
