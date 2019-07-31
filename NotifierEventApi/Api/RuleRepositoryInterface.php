<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\NotifierEventApi\Api;

/**
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
interface RuleRepositoryInterface
{
    /**
     * Save Rule
     * @param \MSP\NotifierEventApi\Api\Data\RuleInterface $rule
     * @return int
     */
    public function save(\MSP\NotifierEventApi\Api\Data\RuleInterface $rule): int;

    /**
     * Get Rule by id
     * @param int $ruleId
     * @return \MSP\NotifierEventApi\Api\Data\RuleInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get(int $ruleId): \MSP\NotifierEventApi\Api\Data\RuleInterface;

    /**
     * Delete Rule
     * @param int $ruleId
     * @return void
     */
    public function deleteById(int $ruleId);

    /**
     * Get a list of Rule
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \MSP\NotifierEventApi\Api\RuleSearchResultsInterface
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null
    ): \MSP\NotifierEventApi\Api\RuleSearchResultsInterface;
}
