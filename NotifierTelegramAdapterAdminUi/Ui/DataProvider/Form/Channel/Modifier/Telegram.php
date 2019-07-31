<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\NotifierTelegramAdapterAdminUi\Ui\DataProvider\Form\Channel\Modifier;

use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Fieldset;
use MSP\NotifierAdminUi\Model\Channel\ModifierInterface;

class Telegram implements ModifierInterface
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
                        'label' => __('Telegram Configuration'),
                        'collapsible' => false,
                    ],
                ],
            ],
            'children' => [
                'token' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Field::NAME,
                                'label' => __('Bot Token'),
                                'dataType' => 'text',
                                'formElement' => 'input',
                                'sortOrder' => 10,
                                'dataScope' => 'general.configuration.token',
                                'validation' => [
                                    'required-entry' => true,
                                ],
                            ],
                        ],
                    ],
                ],
                'chat_id' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Field::NAME,
                                'label' => __('Chat ID'),
                                'dataType' => 'text',
                                'formElement' => 'input',
                                'sortOrder' => 20,
                                'dataScope' => 'general.configuration.chat_id',
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
        return \MSP\NotifierTelegramAdapter\Model\AdapterEngine\Telegram::ADAPTER_CODE;
    }
}
