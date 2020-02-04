<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Notifier\Model;

use Magento\NotifierApi\Api\AdapterPoolInterface;
use Magento\NotifierApi\Api\ChannelRepositoryInterface;
use Magento\NotifierApi\Api\Data\AdapterInterface;
use Magento\NotifierApi\Api\GetAdapterByChannelCodeInterface;
use Magento\NotifierApi\Exception\NotifierChannelDisabledException;

class GetAdapterByChannelCode implements GetAdapterByChannelCodeInterface
{
    /**
     * @var ChannelRepositoryInterface
     */
    private $channelRepository;

    /**
     * @var AdapterPoolInterface
     */
    private $adaptersPool;

    public function __construct(
        ChannelRepositoryInterface $channelRepository,
        AdapterPoolInterface $adaptersPool
    ) {
        $this->channelRepository = $channelRepository;
        $this->adaptersPool = $adaptersPool;
    }

    public function execute(string $channelCode): AdapterInterface
    {
        $channel = $this->channelRepository->getByCode($channelCode);
        if (!$channel->getEnabled()) {
            throw new NotifierChannelDisabledException(__('Notifier channel ' . $channelCode . ' is disabled.'));
        }

        return $this->adaptersPool->getAdapterByCode($channel->getAdapterCode());
    }

}
