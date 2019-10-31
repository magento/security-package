<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierAdminUi\Ui\DataProvider\Form;

use Magento\Backend\Model\UrlInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Magento\NotifierAdminUi\Model\Channel\ModifierInterface;
use Magento\NotifierApi\Api\ChannelRepositoryInterface;
use Magento\NotifierApi\Api\Data\ChannelInterface;

class ChannelDataProvider extends DataProvider
{
    /**
     * @var string
     */
    private $channelAdapterCode;

    /**
     * @var ChannelRepositoryInterface
     */
    private $channelRepository;

    /**
     * @var PoolInterface
     */
    private $modifierPool;

    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * ChannelDataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param ReportingInterface $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param PoolInterface $modifierPool
     * @param ChannelRepositoryInterface $channelRepository
     * @param UrlInterface $url
     * @param array $meta
     * @param array $data
     * @SuppressWarnings(PHPMD.LongVariable)
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
        UrlInterface $url,
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

        $this->channelRepository = $channelRepository;
        $this->modifierPool = $modifierPool;
        $this->url = $url;
    }

    /**
     * Get current channel
     * @return string
     * @throws NoSuchEntityException
     */
    private function getChannelAdapterCode(): string
    {
        if ($this->channelAdapterCode === null) {
            $channelId = (int) $this->request->getParam($this->requestFieldName);
            if ($channelId) {
                $this->channelAdapterCode = $this->channelRepository->get($channelId)->getAdapterCode();
            } else {
                $this->channelAdapterCode = $this->request->getParam('adapter_code');
            }
        }

        return $this->channelAdapterCode;
    }

    /**
     * @inheritdoc
     */
    public function getConfigData(): array
    {
        $config = parent::getConfigData();

        $channelAdapterCode = $this->getChannelAdapterCode();
        if ($channelAdapterCode) {
            $config['submit_url'] = $this->url->getUrl('magento_notifier/channel/save', [
                'adapter_code' => $channelAdapterCode,
            ]);
        }

        return $config;
    }

    /**
     * @inheritdoc
     */
    public function getData(): array
    {
        $data = parent::getData();

        $channelAdapterCode = $this->getChannelAdapterCode();

        $modifiers = $this->modifierPool->getModifiersInstances();
        foreach ($modifiers as $modifier) {
            if ($modifier instanceof ModifierInterface) {
                if (!$channelAdapterCode || ($modifier->getAdapterCode() !== $channelAdapterCode)) {
                    continue;
                }
            }

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

        $channelAdapterCode = $this->getChannelAdapterCode();

        $modifiers = $this->modifierPool->getModifiersInstances();
        foreach ($modifiers as $modifier) {
            if ($modifier instanceof ModifierInterface) {
                if (!$channelAdapterCode || ($modifier->getAdapterCode() !== $channelAdapterCode)) {
                    continue;
                }
            }

            $meta = $modifier->modifyMeta($meta);
        }

        return $meta;
    }
}
