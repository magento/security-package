<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierEvent\Model;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\NotifierEvent\Model\Rule\Command\DeleteInterface;
use Magento\NotifierEvent\Model\Rule\Command\GetInterface;
use Magento\NotifierEvent\Model\Rule\Command\GetListInterface;
use Magento\NotifierEvent\Model\Rule\Command\SaveInterface;
use Magento\NotifierEventApi\Api\Data\RuleInterface;
use Magento\NotifierEventApi\Api\RuleRepositoryInterface;
use Magento\NotifierEventApi\Api\RuleSearchResultsInterface;

/**
 * @SuppressWarnings(PHPMD.ShortVariable)
 * @SuppressWarnings(PHPMD.LongVariable)
  */
class RuleRepository implements RuleRepositoryInterface
{
    /**
     * @var SaveInterface
     */
    private $commandSave;

    /**
     * @var GetInterface
     */
    private $commandGet;

    /**
     * @var DeleteInterface
     */
    private $commandDeleteById;

    /**
     * @var GetListInterface
     */
    private $commandGetList;

    /**
     * @param SaveInterface $commandSave
     * @param GetInterface $commandGet
     * @param DeleteInterface $commandDeleteById
     * @param GetListInterface $commandGetList
     */
    public function __construct(
        SaveInterface $commandSave,
        GetInterface $commandGet,
        DeleteInterface $commandDeleteById,
        GetListInterface $commandGetList
    ) {
        $this->commandSave = $commandSave;
        $this->commandGet = $commandGet;
        $this->commandDeleteById = $commandDeleteById;
        $this->commandGetList = $commandGetList;
    }

    /**
     * @inheritdoc
     */
    public function save(RuleInterface $rule): int
    {
        return $this->commandSave->execute($rule);
    }

    /**
     * @inheritdoc
     */
    public function get(int $ruleId): RuleInterface
    {
        return $this->commandGet->execute($ruleId);
    }

    /**
     * @inheritdoc
     */
    public function deleteById(int $ruleId): void
    {
        $this->commandDeleteById->execute($ruleId);
    }

    /**
     * @inheritdoc
     */
    public function getList(
        SearchCriteriaInterface $searchCriteria = null
    ): RuleSearchResultsInterface {
        return $this->commandGetList->execute($searchCriteria);
    }
}
