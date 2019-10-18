<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierEvent\Model\Rule\Command;

use Exception;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\NotifierEvent\Model\ResourceModel\Rule;
use Magento\NotifierEventApi\Api\Data\RuleInterface;
use Psr\Log\LoggerInterface;

/**
 * @inheritdoc
 */
class Delete implements DeleteInterface
{
    /**
     * @var Rule
     */
    private $resource;

    /**
     * @var GetInterface
     */
    private $commandGet;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Rule $resource
     * @param GetInterface $commandGet
     * @param LoggerInterface $logger
     */
    public function __construct(
        Rule $resource,
        GetInterface $commandGet,
        LoggerInterface $logger
    ) {
        $this->resource = $resource;
        $this->commandGet = $commandGet;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function execute(int $ruleId): void
    {
        /** @var RuleInterface $rule */
        try {
            $rule = $this->commandGet->execute($ruleId);
        } catch (NoSuchEntityException $e) {
            return;
        }

        try {
            $this->resource->delete($rule);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
            throw new CouldNotDeleteException(__('Could not delete Rule'), $e);
        }
    }
}
