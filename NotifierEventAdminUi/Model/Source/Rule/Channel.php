<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierEventAdminUi\Model\Source\Rule;

use Magento\NotifierApi\Api\ChannelRepositoryInterface;

class Channel implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var ChannelRepositoryInterface
     */
    private $channelRepository;

    /**
     * @param ChannelRepositoryInterface $channelRepository
     */
    public function __construct(
        ChannelRepositoryInterface $channelRepository
    ) {
        $this->channelRepository = $channelRepository;
    }

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        $res = [];
        $channels = $this->channelRepository->getList()->getItems();

        foreach ($channels as $channel) {
            $res[] = [
                'value' => $channel->getCode(),
                'label' => $channel->getName(),
            ];
        }

        return $res;
    }
}
