<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierEventApi\Api;

/**
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
interface RuleRepositoryInterface
{
    /**
     * Save Rule
     * @param \Magento\NotifierEventApi\Api\Data\RuleInterface $rule
     * @return int
     */
    public function save(\Magento\NotifierEventApi\Api\Data\RuleInterface $rule): int;

    /**
     * Get Rule by id
     * @param int $ruleId
     * @return \Magento\NotifierEventApi\Api\Data\RuleInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get(int $ruleId): \Magento\NotifierEventApi\Api\Data\RuleInterface;

    /**
     * Delete Rule
     * @param int $ruleId
     * @return void
     */
    public function deleteById(int $ruleId);

    /**
     * Get a list of Rule
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\NotifierEventApi\Api\RuleSearchResultsInterface
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null
    ): \Magento\NotifierEventApi\Api\RuleSearchResultsInterface;
}
