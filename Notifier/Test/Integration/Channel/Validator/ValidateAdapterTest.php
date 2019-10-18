<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocMissingThrowsInspection */

declare(strict_types=1);

namespace Magento\Notifier\Test\Integration\Channel\Validator;

use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Notifier\Model\Channel;
use Magento\Notifier\Model\Channel\Validator\ValidateAdapter;
use Magento\Notifier\Test\Integration\Mock\ConfigureMockAdapter;
use PHPUnit\Framework\TestCase;

class ValidateAdapterTest extends TestCase
{
    /**
     * @var ValidateAdapter
     */
    private $subject;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        ConfigureMockAdapter::execute();
        $this->objectManager = Bootstrap::getObjectManager();
        $this->subject = $this->objectManager->get(ValidateAdapter::class);
    }

    /**
     * @return array
     */
    public function invalidChannelDataProvider(): array
    {
        return [
            [
                'channelData' => [
                    'adapter_code' => '',
                    'configuration_json' => '{}'
                ],
                'errorMessage' => 'Invalid adapter code'
            ],
            [
                'channelData' => [
                    'adapter_code' => 'unknown_adapter',
                    'configuration_json' => '{}'
                ],
                'errorMessage' => 'Invalid adapter code'
            ]
        ];
    }

    /**
     * @return array
     */
    public function validChannelDataProvider(): array
    {
        return [
            [
                'channelData' => [
                    'adapter_code' => 'fake',
                    'configuration_json' => '{}'
                ]
            ]
        ];
    }

    /**
     * @param array $channelData
     * @param string $errorMessage
     * @dataProvider invalidChannelDataProvider
     */
    public function testShouldTriggerValidationException(array $channelData, string $errorMessage): void
    {
        $channel = $this->objectManager->create(
            Channel::class,
            [
                'data' => $channelData
            ]
        );

        $this->expectException(ValidatorException::class);
        $this->expectExceptionMessage($errorMessage);

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->subject->execute($channel);
    }

    /**
     * @param array $channelData
     * @dataProvider validChannelDataProvider
     */
    public function testShouldValidateChannel(array $channelData): void
    {
        $channel = $this->objectManager->create(
            Channel::class,
            [
                'data' => $channelData
            ]
        );

        $this->subject->execute($channel);
    }
}
