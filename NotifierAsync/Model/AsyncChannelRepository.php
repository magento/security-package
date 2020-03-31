<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\NotifierAsync\Model;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Notifier\Model\ChannelRepository;
use Magento\NotifierApi\Api\ChannelRepositoryInterface;
use Magento\NotifierApi\Api\ChannelSearchResultsInterface;
use Magento\NotifierApi\Api\Data\ChannelInterface;
use Magento\NotifierAsync\Model\ResourceModel\GetChannelExtensionAttributes;
use Magento\NotifierAsync\Model\ResourceModel\SaveChannelExtensionAttributes;

/**
 * Async channel repository.
 */
class AsyncChannelRepository implements ChannelRepositoryInterface
{
    /**
     * @var ChannelRepository
     */
    private $channelRepository;

    /**
     * @var GetChannelExtensionAttributes
     */
    private $getChannelExtensionAttributes;

    /**
     * @var ChannelExtensionFactory
     */
    private $channelExtensionFactory;

    public function __construct(
        ChannelRepository $channelRepository,
        GetChannelExtensionAttributes $getChannelExtensionAttributes,
        ChannelExtensionFactory $channelExtensionFactory,
        SaveChannelExtensionAttributes $saveChannelExtensionAttributes
    ) {
        $this->channelRepository = $channelRepository;
        $this->getChannelExtensionAttributes = $getChannelExtensionAttributes;
        $this->channelExtensionFactory = $channelExtensionFactory;
    }

    /**
     * @inheritdoc
     */
    public function save(ChannelInterface $channel): int
    {
        $channelId = $this->channelRepository->save($channel);
        $this->saveExtensionAttributes($channel);

        return $channelId;
    }

    /**
     * @inheritdoc
     */
    public function get(int $channelId): ChannelInterface
    {
        $channel = $this->channelRepository->get($channelId);
        $this->addExtensionAttributes($channel);

        return $channel;
    }

    /**
     * @inheritdoc
     */
    public function getByCode(string $code): ChannelInterface
    {
        $channel = $this->channelRepository->getByCode($code);
        $this->addExtensionAttributes($channel);

        return $channel;
    }

    /**
     * @inheritdoc
     */
    public function deleteById(int $channelId): void
    {
        $this->channelRepository->deleteById($channelId);
    }

    /**
     * @inheritdoc
     */
    public function getList(
        SearchCriteriaInterface $searchCriteria = null
    ): ChannelSearchResultsInterface
    {
        return $this->channelRepository->getList($searchCriteria);
    }


    /**
     * Add extension attributes.
     *
     * @param ChannelInterface $channel
     */
    private function addExtensionAttributes(ChannelInterface $channel): void
    {
        if ($channel->getExtensionAttributes() !== null) {
            $channel->setExtensionAttributes($this->channelExtensionFactory->create());
        }

        $extAttrs = $this->getChannelExtensionAttributes->execute((int)$channel->getId());

        $channel->getExtensionAttributes()->setSendAsync((bool)$extAttrs['send_async']);
    }

    /**
     * Save extension attributes.
     *
     * @param ChannelInterface $channel
     */
    private function saveExtensionAttributes(ChannelInterface $channel): void
    {
        if ($channel->getExtensionAttributes() !== null) {
            $this->saveChannelExtensionAttributes->execute(
                $channel->getId(),
                [
                    'send_async' => (bool)$channel->getExtensionAttributes()->getSendAsync()
                ]
            );
        }
    }
}
