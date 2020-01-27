<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierTemplateApi\Api;

/**
 * @api
 */
interface DatabaseTemplateRepositoryInterface
{
    /**
     * Save DatabaseTemplate
     * @param \Magento\NotifierTemplateApi\Api\Data\DatabaseTemplateInterface $databaseTemplate
     * @return int
     */
    public function save(\Magento\NotifierTemplateApi\Api\Data\DatabaseTemplateInterface $databaseTemplate): int;

    /**
     * Get DatabaseTemplate by id
     * @param int $databaseTemplateIid
     * @return \Magento\NotifierTemplateApi\Api\Data\DatabaseTemplateInterface
     */
    public function get(int $databaseTemplateIid): \Magento\NotifierTemplateApi\Api\Data\DatabaseTemplateInterface;

    /**
     * Get by Code value
     * @param string $code
     * @return \Magento\NotifierTemplateApi\Api\Data\DatabaseTemplateInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByCode(string $code): \Magento\NotifierTemplateApi\Api\Data\DatabaseTemplateInterface;

    /**
     * Get by AdapterCode value
     * @param string $adapterCode
     * @return \Magento\NotifierTemplateApi\Api\Data\DatabaseTemplateInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByAdapterCode(string $adapterCode): \Magento\NotifierTemplateApi\Api\Data\DatabaseTemplateInterface;

    /**
     * Delete DatabaseTemplate
     * @param int $databaseTemplateIid
     * @return void
     */
    public function deleteById(int $databaseTemplateIid): void;

    /**
     * Get a list of DatabaseTemplate
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\NotifierTemplateApi\Api\DatabaseTemplateSearchResultsInterface
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null
    ): \Magento\NotifierTemplateApi\Api\DatabaseTemplateSearchResultsInterface;
}
