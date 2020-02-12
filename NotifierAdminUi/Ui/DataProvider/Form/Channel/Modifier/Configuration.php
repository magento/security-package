<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierAdminUi\Ui\DataProvider\Form\Channel\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\Serialize\SerializerInterface;

class Configuration extends AbstractModifier
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * Params constructor.
     * @param SerializerInterface $serializer
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function __construct(
        SerializerInterface $serializer
    ) {
        $this->serializer = $serializer;
    }

    /**
     * @inheritdoc
     */
    public function modifyData(array $data): array
    {
        // Unpack configuration
        foreach ($data['items'] as &$item) {
            $item['configuration'] = $this->serializer->unserialize($item['configuration_json']);
        }

        return $data;
    }

    /**
     * @inheritdoc
     */
    public function modifyMeta(array $meta): array
    {
        return $meta;
    }
}
