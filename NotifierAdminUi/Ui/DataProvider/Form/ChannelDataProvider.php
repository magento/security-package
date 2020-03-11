<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierAdminUi\Ui\DataProvider\Form;

use Magento\Backend\Model\UrlInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Magento\NotifierAdminUi\Model\Channel\ModifierInterface;
use Magento\NotifierApi\Api\ChannelRepositoryInterface;
use Magento\Notifier\Model\ResourceModel\Channel\CollectionFactory;
use Magento\Ui\DataProvider\AbstractDataProvider;

/**
 * Class Channel Data Provider
 */
class ChannelDataProvider extends AbstractDataProvider
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

    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        CollectionFactory $collection,
        RequestInterface $request,
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
            $meta,
            $data
        );

        $this->collection = $collection->create();
        $this->request = $request;
        $this->channelRepository = $channelRepository;
        $this->modifierPool = $modifierPool;
        $this->url = $url;
    }

    /**
     * Get current channel
     *
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
