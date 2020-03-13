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
use Magento\Notifier\Model\ResourceModel\Channel\CollectionFactory;
use Magento\NotifierAdminUi\Model\Channel\ModifierInterface;
use Magento\NotifierApi\Api\ChannelRepositoryInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Ui\DataProvider\Modifier\PoolInterface;

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

    /**
     * @var RequestInterface
     */
    private $request;

    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        RequestInterface $request = null,
        PoolInterface $modifierPool = null,
        ChannelRepositoryInterface $channelRepository = null,
        UrlInterface $url = null,
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

        $this->collection = \Magento\Framework\App\ObjectManager::getInstance()
            ->get(CollectionFactory::class)
            ->create();

        $this->request = $request ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Framework\App\RequestInterface::class);
        $this->modifierPool = $modifierPool ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Ui\DataProvider\Modifier\PoolInterface::class);
        $this->channelRepository = $channelRepository ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\NotifierApi\Api\ChannelRepositoryInterface::class);
        $this->url = $url ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Backend\Model\UrlInterface::class);
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
