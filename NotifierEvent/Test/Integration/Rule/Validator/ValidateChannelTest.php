<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocMissingThrowsInspection */

declare(strict_types=1);

namespace Magento\NotifierEvent\Test\Integration\Rule\Validator;

use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\NotifierEvent\Model\Rule\Validator\ValidateChannel;
use Magento\NotifierEventApi\Api\Data\RuleInterface;
use PHPUnit\Framework\TestCase;

class ValidateChannelTest extends TestCase
{
    /**
     * @var ValidateChannel
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
        $this->objectManager = Bootstrap::getObjectManager();
        $this->subject = $this->objectManager->get(ValidateChannel::class);
    }

    /**
     * @return array
     */
    public function invalidDataProvider(): array
    {
        return [
            [
                'data' => [
                    'channels_codes' => ''
                ],
                'errorMessage' => 'You must specify at least one channel'
            ],
            [
                'data' => [
                    'channels_codes' => 'invalid content'
                ],
                'errorMessage' => 'Invalid channels data format'
            ],
            [
                'data' => [
                    'channels_codes' => '[]'
                ],
                'errorMessage' => 'You must specify at least one channel'
            ]
        ];
    }

    /**
     * @return array
     */
    public function validDataProvider(): array
    {
        return [
            [
                'data' => [
                    'channels_codes' => '["channel_code"]'
                ]
            ],
            [
                'data' => [
                    'channels_codes' => '["channel1", "channel2"]'
                ]
            ]
        ];
    }

    /**
     * @param array $data
     * @param string $errorMessage
     * @dataProvider invalidDataProvider
     */
    public function testShouldTriggerValidationException(array $data, string $errorMessage): void
    {
        $channel = $this->objectManager->create(
            RuleInterface::class,
            [
                'data' => $data
            ]
        );

        $this->expectException(ValidatorException::class);
        $this->expectExceptionMessage($errorMessage);

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->subject->execute($channel);
    }

    /**
     * @param array $data
     * @dataProvider validDataProvider
     */
    public function testShouldValidateChannel(array $data): void
    {
        $channel = $this->objectManager->create(
            RuleInterface::class,
            [
                'data' => $data
            ]
        );

        $this->subject->execute($channel);
    }
}
