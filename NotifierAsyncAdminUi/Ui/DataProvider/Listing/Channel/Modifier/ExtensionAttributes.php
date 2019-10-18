<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierAsyncAdminUi\Ui\DataProvider\Listing\Channel\Modifier;

use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\NotifierApi\Api\Data\ChannelInterface;
use Magento\NotifierAsyncAdminUi\Model\DecorateChannelDataProvider;

class ExtensionAttributes implements ModifierInterface
{
    /**
     * @var DecorateChannelDataProvider
     */
    private $decorateChannelDataProvider;

    /**
     * @param DecorateChannelDataProvider $decorateChannelDataProvider
     */
    public function __construct(
        DecorateChannelDataProvider $decorateChannelDataProvider
    ) {
        $this->decorateChannelDataProvider = $decorateChannelDataProvider;
    }

    /**
     * @inheritDoc
     */
    public function modifyData(array $data)
    {
        foreach ($data['items'] as &$item) {
            $item = $this->decorateChannelDataProvider->execute(
                (int) $item['channel_id'],
                $item
            );
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function modifyMeta(array $meta)
    {
        return $meta;
    }
}
