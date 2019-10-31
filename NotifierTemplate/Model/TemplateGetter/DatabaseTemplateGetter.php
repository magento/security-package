<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierTemplate\Model\TemplateGetter;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\NotifierApi\Api\ChannelRepositoryInterface;
use Magento\NotifierTemplate\Model\ResourceModel\DatabaseTemplate\Collection;
use Magento\NotifierTemplate\Model\ResourceModel\DatabaseTemplate\CollectionFactory;
use Magento\NotifierTemplateApi\Api\Data\DatabaseTemplateInterface;
use Magento\NotifierTemplateApi\Model\TemplateGetter\TemplateGetterInterface;

class DatabaseTemplateGetter implements TemplateGetterInterface
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var ChannelRepositoryInterface
     */
    private $channelRepository;

    public function __construct(
        CollectionFactory $collectionFactory,
        ChannelRepositoryInterface $channelRepository
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->channelRepository = $channelRepository;
    }

    /**
     * @inheritdoc
     */
    public function getTemplate(string $channelCode, string $templateId): ?string
    {
        try {
            $channel = $this->channelRepository->getByCode($channelCode);
            $adapterCode = $channel->getAdapterCode();
        } catch (NoSuchEntityException $e) {
            $adapterCode = '';
        }

        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->filterAdapterCandidates($adapterCode, $templateId);

        if ($collection->getSize()) {
            return (string) $collection->getFirstItem()->getTemplate();
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function getList(): array
    {
        $res = [];

        $collection = $this->collectionFactory->create();
        foreach ($collection as $template) {
            /** @var DatabaseTemplateInterface $template */
            $res[$template->getCode()] = [
                'label' => $template->getName(),
            ];
        }

        return $res;
    }
}
