<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Notifier\Model\Channel\Command;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Notifier\Model\ResourceModel\Channel;
use Magento\NotifierApi\Api\Data\ChannelInterface;
use Magento\NotifierApi\Api\Data\ChannelInterfaceFactory;

/**
 * @inheritdoc
 */
class Get implements GetInterface
{
    /**
     * @var Channel
     */
    private $resource;

    /**
     * @var ChannelInterfaceFactory
     */
    private $factory;

    /**
     * @param Channel $resource
     * @param ChannelInterfaceFactory $factory
     */
    public function __construct(
        Channel $resource,
        ChannelInterfaceFactory $factory
    ) {
        $this->resource = $resource;
        $this->factory = $factory;
    }

    /**
     * @inheritdoc
     */
    public function execute(int $channelId): ChannelInterface
    {
        /** @var ChannelInterface $channel */
        $channel = $this->factory->create();
        $this->resource->load(
            $channel,
            $channelId,
            'channel_id'
        );

        if (null === $channel->getId()) {
            throw new NoSuchEntityException(__('Channel with id "%value" does not exist.', [
                'value' => $channelId
            ]));
        }

        return $channel;
    }
}
