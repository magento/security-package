<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierAsyncAdminUi\Model;

use Magento\NotifierAsync\Model\ResourceModel\GetChannelExtensionAttributes;

class DecorateChannelDataProvider
{
    /**
     * @var GetChannelExtensionAttributes
     */
    private $getChannelExtensionAttributes;

    /**
     * @param GetChannelExtensionAttributes $getChannelExtensionAttributes
     */
    public function __construct(GetChannelExtensionAttributes $getChannelExtensionAttributes)
    {
        $this->getChannelExtensionAttributes = $getChannelExtensionAttributes;
    }

    /**
     * @param int $channelId
     * @param array $data
     * @return array
     */
    public function execute(int $channelId, array $data): array
    {
        $extAttrs = $this->getChannelExtensionAttributes->execute($channelId);
        return array_merge(
            $data,
            $extAttrs
        );
    }
}
