<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierEventBridgeAdapterAdminUi\Ui\DataProvider\Form\Channel\Modifier;

use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Fieldset;
use Magento\NotifierAdminUi\Model\Channel\ModifierInterface;

class EventBridge implements ModifierInterface
{
    /**
     * @inheritdoc
     */
    public function modifyMeta(array $meta): array
    {
        $meta['configuration'] = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Fieldset::NAME,
                        'label' => __('Event Bridge Configuration'),
                        'collapsible' => false,
                    ],
                ],
            ],
            'children' => [
                'api_version' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Field::NAME,
                                'label' => __('Version'),
                                'dataType' => 'text',
                                'formElement' => 'input',
                                'sortOrder' => 10,
                                'dataScope' => 'general.configuration.api_version',
                                'validation' => [
                                    'required-entry' => true,
                                ],
                            ],
                        ],
                    ],
                ],
                'api_key' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Field::NAME,
                                'label' => __('API Key'),
                                'dataType' => 'text',
                                'formElement' => 'input',
                                'sortOrder' => 20,
                                'dataScope' => 'general.configuration.api_key',
                                'validation' => [
                                    'required-entry' => true,
                                ],
                            ],
                        ],
                    ],
                ],
                'api_secret' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Field::NAME,
                                'label' => __('API Secret'),
                                'dataType' => 'text',
                                'formElement' => 'input',
                                'sortOrder' => 30,
                                'dataScope' => 'general.configuration.api_secret',
                                'validation' => [
                                    'required-entry' => true,
                                ],
                            ],
                        ],
                    ],
                ],
                'region' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Field::NAME,
                                'label' => __('Region'),
                                'dataType' => 'text',
                                'formElement' => 'input',
                                'sortOrder' => 40,
                                'dataScope' => 'general.configuration.region',
                                'validation' => [
                                    'required-entry' => true,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        return $meta;
    }

    /**
     * @inheritdoc
     */
    public function modifyData(array $data): array
    {
        return $data;
    }

    /**
     * @inheritdoc
     */
    public function getAdapterCode(): string
    {
        return \Magento\NotifierEventBridgeAdapter\Model\AdapterEngine\EventBridge::ADAPTER_CODE;
    }
}
