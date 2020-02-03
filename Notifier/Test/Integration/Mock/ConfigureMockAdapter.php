<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Notifier\Test\Integration\Mock;

use Magento\NotifierApi\Model\AdapterEnginePool;
use Magento\NotifierApi\Model\AdapterValidatorPool;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\NotifierApi\Model\Adapter;
use Magento\NotifierApi\Model\AdapterEngine\AdapterValidator;
use Magento\NotifierApi\Model\AdapterPool;

class ConfigureMockAdapter
{
    /**
     * Configure object manager to use a fake adapter
     */
    public static function execute(): void
    {
        $objectManager = Bootstrap::getObjectManager();

        $objectManager->configure([
            \Magento\NotifierApi\Test\Integration\Mock\FakeAdapter\Validator::class => [
                'type' => ltrim(AdapterValidator::class, '\\'),
                'arguments' => [
                    'messageValidators' => [],
                    'paramsValidators' => [],
                ]
            ],
            \Magento\NotifierApi\Test\Integration\Mock\FakeAdapter::class => [
                'type' => ltrim(Adapter::class, '\\'),
                'arguments' => [
                    'code' => 'fake',
                    'name' => 'Fake Adapter',
                    'description' => 'Fake Adapter'
                ]
            ],
            ltrim(AdapterPool::class, '\\') => [
                'arguments' => [
                    'adapters' => [
                        'fake' => [
                            'instance' => \Magento\NotifierApi\Test\Integration\Mock\FakeAdapter::class
                        ]
                    ]
                ]
            ],
            ltrim(AdapterEnginePool::class, '\\') => [
                'arguments' => [
                    'adapterEngines' => [
                        'fake' => [
                            'instance' => \Magento\Notifier\Test\Integration\Mock\FakeAdapterEngine::class
                        ]
                    ]
                ]
            ],
            ltrim(AdapterValidatorPool::class, '\\') => [
                'arguments' => [
                    'adapterValidators' => [
                        'fake' => [
                            'instance' => \Magento\NotifierApi\Test\Integration\Mock\FakeAdapter\Validator::class
                        ]
                    ]
                ]
            ],
        ]);
    }
}
