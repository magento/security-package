<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model\ResourceModel;

use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResults;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\TwoFactorAuth\Api\Data\TrustedInterface;
use Magento\TwoFactorAuth\Api\Data\TrustedInterfaceFactory;
use Magento\TwoFactorAuth\Api\Data\TrustedSearchResultsInterface;
use Magento\TwoFactorAuth\Api\Data\TrustedSearchResultsInterfaceFactory;
use Magento\TwoFactorAuth\Api\TrustedRepositoryInterface;
use Magento\TwoFactorAuth\Model\ResourceModel\Trusted\CollectionFactory;
use Magento\TwoFactorAuth\Model\TrustedFactory;
use Magento\TwoFactorAuth\Model\TrustedRegistry;

/**
 * @inheritDoc
 */
class TrustedRepository implements TrustedRepositoryInterface
{
    /**
     * @var  TrustedInterfaceFactory
     */
    private $trustedFactory;

    /**
     * @var Trusted
     */
    private $resource;

    /**
     * @var TrustedSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var TrustedRegistry
     */
    private $registry;

    /**
     * @var ExtensibleDataObjectConverter
     */
    private $extensibleDataObjectConverter;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @param TrustedFactory $trustedFactory
     * @param Trusted $resource
     * @param CollectionFactory $collectionFactory
     * @param TrustedSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param TrustedRegistry $registry
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        TrustedFactory $trustedFactory,
        Trusted $resource,
        CollectionFactory $collectionFactory,
        TrustedSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        TrustedRegistry $registry,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->searchResultsFactory = $searchResultsFactory;
        $this->trustedFactory = $trustedFactory;
        $this->resource = $resource;
        $this->registry = $registry;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @inheritDoc
     */
    public function save(TrustedInterface $trusted): TrustedInterface
    {
        trigger_error('Trusted devices are no longer supported', E_USER_DEPRECATED);

        $trustedData = $this->extensibleDataObjectConverter->toNestedArray(
            $trusted,
            [],
            TrustedInterface::class
        );

        /** @var \Magento\TwoFactorAuth\Model\Trusted $trustedModel */
        $trustedModel = $this->trustedFactory->create(['data' => $trustedData]);
        $trustedModel->setDataChanges(true);
        $this->resource->save($trustedModel);
        $trusted->setId($trustedModel->getId());

        $this->registry->push($trustedModel);

        return $this->getById($trustedModel->getId());
    }

    /**
     * @inheritDoc
     * @throws NoSuchEntityException
     */
    public function getById(int $id): TrustedInterface
    {
        trigger_error('Trusted devices are no longer supported', E_USER_DEPRECATED);

        $fromRegistry = $this->registry->retrieveById($id);
        if ($fromRegistry === null) {
            $trusted = $this->trustedFactory->create();
            $this->resource->load($trusted, $id);

            if (!$trusted->getId()) {
                throw new NoSuchEntityException(__('No such Trusted'));
            }

            $this->registry->push($trusted);
        }

        return $this->registry->retrieveById($id);
    }

    /**
     * @inheritDoc
     * @throws NoSuchEntityException
     */
    public function getByUserId(int $value): TrustedInterface
    {
        trigger_error('Trusted devices are no longer supported', E_USER_DEPRECATED);

        $fromRegistry = $this->registry->retrieveByUserId($value);
        if ($fromRegistry === null) {
            $trusted = $this->trustedFactory->create();
            $this->resource->load($trusted, $value, 'user_id');

            if (!$trusted->getId()) {
                throw new NoSuchEntityException(__('No such Trusted'));
            }

            $this->registry->push($trusted);
        }

        return $this->registry->retrieveByUserId($value);
    }

    /**
     * @inheritDoc
     */
    public function delete(TrustedInterface $trusted): void
    {
        trigger_error('Trusted devices are no longer supported', E_USER_DEPRECATED);

        $trustedData = $this->extensibleDataObjectConverter->toNestedArray(
            $trusted,
            [],
            TrustedInterface::class
        );

        /** @var \Magento\TwoFactorAuth\Model\Trusted $trustedModel */
        $trustedModel = $this->trustedFactory->create(['data' => $trustedData]);
        $trustedModel->setData($this->resource->getIdFieldName(), $trusted->getId());

        $this->resource->delete($trustedModel);
        $this->registry->removeById($trustedModel->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface
    {
        trigger_error('Trusted devices are no longer supported', E_USER_DEPRECATED);

        $collection = $this->collectionFactory->create();
        if (null === $searchCriteria) {
            $searchCriteria = $this->searchCriteriaBuilder->create();
        } else {
            $this->collectionProcessor->process($searchCriteria, $collection);
        }

        /** @var TrustedSearchResultsInterface $searchResult */
        $searchResult = $this->searchResultsFactory->create();
        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());
        $searchResult->setSearchCriteria($searchCriteria);

        return $searchResult;
    }
}
