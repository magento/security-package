<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Notifier\Model\Channel\Command;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Notifier\Model\ResourceModel\Channel\Collection;
use Magento\Notifier\Model\ResourceModel\Channel\CollectionFactory;
use Magento\NotifierApi\Api\ChannelSearchResultsInterface;
use Magento\NotifierApi\Api\ChannelSearchResultsInterfaceFactory as SearchResultsFactory;
use Magento\NotifierApi\Api\Data\ChannelExtensionInterfaceFactory;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;

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
     * @var SearchResultsFactory
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
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var JoinProcessorInterface
     */
    private $joinProcessor;

    /**
     * @var ChannelExtensionInterfaceFactory
     */
    private $operationExtensionFactory;

    /**
     * @param CollectionFactory $collectionFactory
     * @param SearchResultsFactory $searchResultsFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param CollectionProcessorInterface $collectionProcessor
     * @param EntityManager $entityManager
     * @param JoinProcessorInterface $joinProcessor
     * @param ChannelExtensionInterfaceFactory $operationExtension
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        SearchResultsFactory $searchResultsFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CollectionProcessorInterface $collectionProcessor,
        EntityManager $entityManager,
        JoinProcessorInterface $joinProcessor,
        ChannelExtensionInterfaceFactory $operationExtension
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->collectionProcessor = $collectionProcessor;
        $this->entityManager = $entityManager;
        $this->joinProcessor = $joinProcessor;
        $this->operationExtensionFactory = $operationExtension;
    }

    /**
     * @inheritdoc
     */
    public function execute(SearchCriteriaInterface $searchCriteria = null): ChannelSearchResultsInterface
    {
        /** @var ChannelSearchResultsInterface $searchResult */
        $searchResult = $this->searchResultsFactory->create();

        if (null === $searchCriteria) {
            $searchCriteria = $this->searchCriteriaBuilder->create();
        }

        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $this->joinProcessor->process(
            $collection,
            \Magento\NotifierApi\Api\Data\ChannelInterface::class
        );
        $this->collectionProcessor->process($searchCriteria, $collection);
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setTotalCount($collection->getSize());
        $searchResult->setItems($collection->getItems());

        return $searchResult;
    }
}
