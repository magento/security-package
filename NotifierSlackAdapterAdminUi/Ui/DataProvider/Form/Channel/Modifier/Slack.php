<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierSlackAdapterAdminUi\Ui\DataProvider\Form\Channel\Modifier;

use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Fieldset;
use Magento\NotifierAdminUi\Model\Channel\ModifierInterface;

class Slack implements ModifierInterface
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
                        'label' => __('Slack Configuration'),
                        'collapsible' => false,
                    ],
                ],
            ],
            'children' => [
                'webhook' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Field::NAME,
                                'label' => __('Webhook'),
                                'dataType' => 'text',
                                'formElement' => 'input',
                                'sortOrder' => 10,
                                'dataScope' => 'general.configuration.webhook',
                                'validation' => [
                                    'required-entry' => true,
                                ],
                            ],
                        ],
                    ],
                ],
                'channel' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Field::NAME,
                                'label' => __('Channel'),
                                'dataType' => 'text',
                                'formElement' => 'input',
                                'sortOrder' => 20,
                                'dataScope' => 'general.configuration.channel',
                                'validation' => [
                                    'required-entry' => true,
                                ],
                            ],
                        ],
                    ],
                ],
                'color' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Field::NAME,
                                'label' => __('Color'),
                                'dataType' => 'text',
                                'formElement' => 'input',
                                'sortOrder' => 30,
                                'dataScope' => 'general.configuration.color',
                            ],
                        ],
                    ],
                ],
                'emoji' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Field::NAME,
                                'label' => __('Emoji code'),
                                'dataType' => 'text',
                                'formElement' => 'input',
                                'sortOrder' => 40,
                                'dataScope' => 'general.configuration.emoji',
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
        return \Magento\NotifierSlackAdapter\Model\AdapterEngine\Slack::ADAPTER_CODE;
    }
}
