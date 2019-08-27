<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\NotifierAsyncAdminUi\Ui\DataProvider\Form\Channel\Modifier;

use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use MSP\NotifierApi\Api\Data\ChannelInterface;
use MSP\NotifierAsyncAdminUi\Model\DecorateChannelDataProvider;

class ExtensionAttributes implements ModifierInterface
{
    /**
     * @var DecorateChannelDataProvider
     */
    private $decorateChannelDataProvider;

    /**
     * @param DecorateChannelDataProvider $decorateChannelDataProvider
     */
    public function __construct(DecorateChannelDataProvider $decorateChannelDataProvider)
    {
        $this->decorateChannelDataProvider = $decorateChannelDataProvider;
    }

    /**
     * @inheritDoc
     */
    public function modifyData(array $data)
    {
        foreach ($data['items'] as &$item) {
            $item['extension_attributes'] = $item['extension_attributes'] ?? [];

            $item['extension_attributes'] = $this->decorateChannelDataProvider->execute(
                (int) $item['channel_id'],
                $item['extension_attributes']
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
