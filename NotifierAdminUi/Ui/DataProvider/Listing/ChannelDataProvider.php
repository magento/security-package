<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierAdminUi\Ui\DataProvider\Listing;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider;
use Magento\NotifierApi\Api\ChannelRepositoryInterface;
use Magento\NotifierApi\Api\Data\ChannelInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Magento\Ui\DataProvider\SearchResultFactory;
use Magento\Framework\Api\Filter;

/**
 * Class Channel Data Provider
 */
class ChannelDataProvider extends DataProvider
{
    /**
     * @var PoolInterface
     */
    private $modifierPool;

    /**
     * @var ChannelRepositoryInterface
     */
    private $channelRepository;

    /**
     * @var SearchResultFactory
     */
    private $searchResultFactory;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param ReportingInterface $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param PoolInterface $modifierPool
     * @param ChannelRepositoryInterface $channelRepository
     * @param array $meta
     * @param array $data
     * @SuppressWarnings(PHPMD.LongVariables)
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        PoolInterface $modifierPool,
        ChannelRepositoryInterface $channelRepository,
        SearchResultFactory $searchResultFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );

        $this->modifierPool = $modifierPool;
        $this->channelRepository = $channelRepository;
        $this->searchResultFactory = $searchResultFactory;
    }

    /**
     * @inheritdoc
     */
    public function getData(): array
    {
        $data = parent::getData();

        $modifiers = $this->modifierPool->getModifiersInstances();
        foreach ($modifiers as $modifier) {
            $data = $modifier->modifyData($data);
        }

        return $data;
    }

    /**
     * @inheritdoc
     */
    public function getMeta(): array
    {
        $meta = parent::getMeta();

        $modifiers = $this->modifierPool->getModifiersInstances();
        foreach ($modifiers as $modifier) {
            $meta = $modifier->modifyMeta($meta);
        }

        return $meta;
    }

    /**
     * @inheritdoc
     */
    public function getSearchResult()
    {
        $searchCriteria = $this->getSearchCriteria();
        $result = $this->channelRepository->getList($searchCriteria);
        $searchResult = $this->searchResultFactory->create(
            $result->getItems(),
            $result->getTotalCount(),
            $searchCriteria,
            ChannelInterface::ID
        );

        return $searchResult;
    }

    /**
     * Add full text search filter to collection
     *
     * @param Filter $filter
     * @return void
     */
    public function addFilter(Filter $filter): void
    {
        if ($filter->getField() !== 'fulltext') {
            parent::addFilter($filter);
        } else {
            $filter->setField('name');
            $filter->setValue('%' . $filter->getValue() . '%');
            $filter->setConditionType('like');
            parent::addFilter($filter);

            $filter->setField('adapter_code');
            $filter->setValue('%' . $filter->getValue() . '%');
            $filter->setConditionType('like');
            parent::addFilter($filter);

            $filter->setField('code');
            $filter->setValue('%' . $filter->getValue() . '%');
            $filter->setConditionType('like');
            parent::addFilter($filter);
        }
    }
}
