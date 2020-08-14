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
use Magento\TwoFactorAuth\Api\Data\UserConfigInterface;
use Magento\TwoFactorAuth\Api\Data\UserConfigInterfaceFactory;
use Magento\TwoFactorAuth\Api\Data\UserConfigSearchResultsInterface;
use Magento\TwoFactorAuth\Api\Data\UserConfigSearchResultsInterfaceFactory;
use Magento\TwoFactorAuth\Api\UserConfigRepositoryInterface;
use Magento\TwoFactorAuth\Model\ResourceModel\UserConfig\Collection;
use Magento\TwoFactorAuth\Model\ResourceModel\UserConfig\CollectionFactory;
use Magento\TwoFactorAuth\Model\UserConfigFactory;
use Magento\TwoFactorAuth\Model\UserConfigRegistry;

/**
 * @inheritDoc
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class UserConfigRepository implements UserConfigRepositoryInterface
{
    /**
     * @var  UserConfigInterfaceFactory
     */
    private $userConfigFactory;

    /**
     * @var UserConfig
     */
    private $resource;

    /**
     * @var UserConfigSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var UserConfigRegistry
     */
    private $registry;

    /**
     * @var ExtensibleDataObjectConverter
     */
    private $extensibleDataObjectConverter;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param UserConfigFactory $userConfigFactory
     * @param CollectionFactory $collectionFactory
     * @param UserConfig $resource
     * @param UserConfigSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param UserConfigRegistry $registry
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        UserConfigFactory $userConfigFactory,
        CollectionFactory $collectionFactory,
        UserConfig $resource,
        UserConfigSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        UserConfigRegistry $registry,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->searchResultsFactory = $searchResultsFactory;
        $this->userConfigFactory = $userConfigFactory;
        $this->resource = $resource;
        $this->registry = $registry;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
        $this->collectionFactory = $collectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @inheritdoc
     */
    public function save(UserConfigInterface $userConfig): UserConfigInterface
    {
        $userConfigData = $this->extensibleDataObjectConverter->toNestedArray(
            $userConfig,
            [],
            UserConfigInterface::class
        );

        /** @var \Magento\TwoFactorAuth\Model\UserConfig $userConfigModel */
        $userConfigModel = $this->userConfigFactory->create(['data' => $userConfigData]);
        $userConfigModel->setDataChanges(true);
        $this->resource->save($userConfigModel);
        $userConfig->setId($userConfigModel->getId());

        $this->registry->push($userConfigModel);

        return $this->getById($userConfigModel->getId());
    }

    /**
     * @inheritdoc
     */
    public function getById(int $id): UserConfigInterface
    {
        $fromRegistry = $this->registry->retrieveById($id);
        if ($fromRegistry === null) {
            $userConfig = $this->userConfigFactory->create();
            $this->resource->load($userConfig, $id);

            if (!$userConfig->getId()) {
                throw new NoSuchEntityException(__('No such UserConfig'));
            }

            $this->registry->push($userConfig);
        }

        return $this->registry->retrieveById($id);
    }

    /**
     * @inheritdoc
     */
    public function getByUserId(int $value): UserConfigInterface
    {
        $fromRegistry = $this->registry->retrieveByUserId($value);
        if ($fromRegistry === null) {
            $userConfig = $this->userConfigFactory->create();
            $this->resource->load($userConfig, $value, 'user_id');

            if (!$userConfig->getId()) {
                throw new NoSuchEntityException(__('No such UserConfig'));
            }

            $this->registry->push($userConfig);
        }

        return $this->registry->retrieveByUserId($value);
    }

    /**
     * @inheritdoc
     */
    public function delete(UserConfigInterface $userConfig): bool
    {
        $userConfigData = $this->extensibleDataObjectConverter->toNestedArray(
            $userConfig,
            [],
            UserConfigInterface::class
        );

        /** @var \Magento\TwoFactorAuth\Model\UserConfig $userConfigModel */
        $userConfigModel = $this->userConfigFactory->create(['data' => $userConfigData]);
        $userConfigModel->setData($this->resource->getIdFieldName(), $userConfig->getId());

        $this->resource->delete($userConfigModel);
        $this->registry->removeById($userConfigModel->getId());

        return true;
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

        /** @var UserConfigSearchResultsInterface $searchResult */
        $searchResult = $this->searchResultsFactory->create();
        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());
        $searchResult->setSearchCriteria($searchCriteria);

        return $searchResult;
    }
}
