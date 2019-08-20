<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\Notifier\Test\Integration\Mock;

use Magento\TestFramework\Helper\Bootstrap;
use MSP\NotifierApi\Model\Adapter;
use MSP\NotifierApi\Model\AdapterEngine\AdapterValidator;
use MSP\NotifierApi\Model\AdaptersPool;

class ConfigureMockAdapter
{
    /**
     * Configure object manager to use a fake adapter
     */
    public static function execute(): void
    {
        $objectManager = Bootstrap::getObjectManager();

        $objectManager->configure([
            \MSP\NotifierApi\Test\Integration\Mock\FakeAdapter\Validator::class => [
                'type' => ltrim(AdapterValidator::class, '\\'),
                'arguments' => [
                    'messageValidators' => [],
                    'paramsValidators' => [],
                ]
            ],
            \MSP\NotifierApi\Test\Integration\Mock\FakeAdapter::class => [
                'type' => ltrim(Adapter::class, '\\'),
                'arguments' => [
                    'engine' => [
                        'instance' => ltrim(FakeAdapterEngine::class, '\\'),
                    ],
                    'validatorChain' => [
                        'instance' => \MSP\NotifierApi\Test\Integration\Mock\FakeAdapter\Validator::class
                    ],
                    'code' => 'fake',
                    'name' => 'Fake Adapter',
                    'description' => 'Fake Adapter'
                ]
            ],
            ltrim(AdaptersPool::class, '\\') => [
                'arguments' => [
                    'adapters' => [
                        'fake' => [
                            'instance' => \MSP\NotifierApi\Test\Integration\Mock\FakeAdapter::class
                        ]
                    ]
                ]
            ]
        ]);
    }
}
