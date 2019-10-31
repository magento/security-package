<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierEvent\Model\Rule\Command;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\NotifierApi\Api\RuleSearchResultsInterface;
use Magento\NotifierEvent\Model\ResourceModel\Rule\Collection;
use Magento\NotifierEvent\Model\ResourceModel\Rule\CollectionFactory;
use Magento\NotifierEventApi\Api\RuleSearchResultsInterfaceFactory;

/**
 * @inheritdoc
 */
class GetList implements GetListInterface
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var RuleSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @param CollectionFactory $collectionFactory
     * @param RuleSearchResultsInterfaceFactory $searchResultsFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param CollectionProcessorInterface $collectionProcessor
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        RuleSearchResultsInterfaceFactory $searchResultsFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritdoc
     */
    public function execute(
        SearchCriteriaInterface $searchCriteria = null
    ): \Magento\NotifierEventApi\Api\RuleSearchResultsInterface {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();

        if (null === $searchCriteria) {
            $searchCriteria = $this->searchCriteriaBuilder->create();
        } else {
            $this->collectionProcessor->process($searchCriteria, $collection);
        }

        /** @var RuleSearchResultsInterface $searchResult */
        $searchResult = $this->searchResultsFactory->create();
        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());
        $searchResult->setSearchCriteria($searchCriteria);

        return $searchResult;
    }
}
