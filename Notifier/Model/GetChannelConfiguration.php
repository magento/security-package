<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Notifier\Model;

use Magento\NotifierApi\Api\Data\ChannelInterface;
use Magento\NotifierApi\Api\GetChannelConfigurationInterface;
use Magento\NotifierApi\Model\SerializerInterface;

/**
 * @inheritdoc
 */
class GetChannelConfiguration implements GetChannelConfigurationInterface
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param SerializerInterface $serializer
     */
    public function __construct(
        SerializerInterface $serializer
    )
    {
        $this->serializer = $serializer;
    }

    /**
     * @inheritdoc
     */
    public function execute(ChannelInterface $channel): array
    {
        return $this->serializer->unserialize($channel->getConfigurationJson());
    }
}
