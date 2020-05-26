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
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\TwoFactorAuth\Api\CountryRepositoryInterface;
use Magento\TwoFactorAuth\Api\Data\CountryInterface;
use Magento\TwoFactorAuth\Api\Data\CountryInterfaceFactory;
use Magento\TwoFactorAuth\Api\Data\CountrySearchResultsInterface;
use Magento\TwoFactorAuth\Api\Data\CountrySearchResultsInterfaceFactory;
use Magento\TwoFactorAuth\Model\CountryFactory;
use Magento\TwoFactorAuth\Model\CountryRegistry;
use Magento\TwoFactorAuth\Model\ResourceModel\Country\Collection;
use Magento\TwoFactorAuth\Model\ResourceModel\Country\CollectionFactory;

/**
 * @inheritDoc
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CountryRepository implements CountryRepositoryInterface
{
    /**
     * @var  CountryInterfaceFactory
     */
    private $countryFactory;

    /**
     * @var Country
     */
    private $resource;

    /**
     * @var CountrySearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var CountryRegistry
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
     * @param CountryFactory $countryFactory
     * @param CollectionFactory $collectionFactory
     * @param Country $resource
     * @param CountrySearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param CountryRegistry $registry
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        CountryFactory $countryFactory,
        CollectionFactory $collectionFactory,
        Country $resource,
        CountrySearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CountryRegistry $registry,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->searchResultsFactory = $searchResultsFactory;
        $this->countryFactory = $countryFactory;
        $this->resource = $resource;
        $this->registry = $registry;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @inheritdoc
     */
    public function save(CountryInterface $country): CountryInterface
    {
        $countryData = $this->extensibleDataObjectConverter->toNestedArray(
            $country,
            [],
            CountryInterface::class
        );

        /** @var \Magento\TwoFactorAuth\Model\Country $countryModel */
        $countryModel = $this->countryFactory->create(['data' => $countryData]);
        $countryModel->setDataChanges(true);
        $this->resource->save($countryModel);
        $country->setId($countryModel->getId());

        $this->registry->push($countryModel);

        return $this->getById($countryModel->getId());
    }

    /**
     * @inheritdoc
     *
     * @throws NoSuchEntityException
     */
    public function getById(int $id): CountryInterface
    {
        $fromRegistry = $this->registry->retrieveById($id);
        if ($fromRegistry === null) {
            $country = $this->countryFactory->create();
            $this->resource->load($country, $id);

            if (!$country->getId()) {
                throw new NoSuchEntityException(__('No such Country'));
            }

            $this->registry->push($country);
        }

        return $this->registry->retrieveById($id);
    }

    /**
     * @inheritdoc
     *
     * @throws NoSuchEntityException
     */
    public function getByCode(string $value): CountryInterface
    {
        $fromRegistry = $this->registry->retrieveByCode($value);
        if ($fromRegistry === null) {
            $country = $this->countryFactory->create();
            $this->resource->load($country, $value, 'code');

            if (!$country->getId()) {
                throw new NoSuchEntityException(__('No such Country'));
            }

            $this->registry->push($country);
        }

        return $this->registry->retrieveByCode($value);
    }

    /**
     * @inheritdoc
     */
    public function delete(CountryInterface $country): void
    {
        $countryData = $this->extensibleDataObjectConverter->toNestedArray(
            $country,
            [],
            CountryInterface::class
        );

        /** @var \Magento\TwoFactorAuth\Model\Country $countryModel */
        $countryModel = $this->countryFactory->create(['data' => $countryData]);
        $countryModel->setData($this->resource->getIdFieldName(), $country->getId());

        $this->resource->delete($countryModel);
        $this->registry->removeById($countryModel->getId());
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();

        if (null === $searchCriteria) {
            $searchCriteria = $this->searchCriteriaBuilder->create();
        } else {
            $this->collectionProcessor->process($searchCriteria, $collection);
        }

        /** @var CountrySearchResultsInterface $searchResult */
        $searchResult = $this->searchResultsFactory->create();
        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());
        $searchResult->setSearchCriteria($searchCriteria);

        return $searchResult;
    }
}
