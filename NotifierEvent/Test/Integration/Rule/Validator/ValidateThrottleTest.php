<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocMissingThrowsInspection */

declare(strict_types=1);

namespace MSP\NotifierEvent\Test\Integration\Rule\Validator;

use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use MSP\NotifierEvent\Model\Rule\Validator\ValidateThrottle;
use MSP\NotifierEventApi\Api\Data\RuleInterface;
use PHPUnit\Framework\TestCase;

class ValidateThrottleTest extends TestCase
{
    /**
     * @var ValidateThrottle
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
        $this->subject = $this->objectManager->get(ValidateThrottle::class);
    }

    /**
     * @return array
     */
    public function invalidDataProvider(): array
    {
        return [
            [
                'data' => [
                    'throttle_interval' => -10,
                    'throttle_limit' => 0,
                ],
                'errorMessage' => 'Throttle interval must be greater or equal to 0'
            ],
            [
                'data' => [
                    'throttle_interval' => 0,
                    'throttle_limit' => -10,
                ],
                'errorMessage' => 'Throttle limit must be greater or equal to 0'
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
                    'throttle_interval' => 10,
                    'throttle_limit' => 10,
                ]
            ],
            [
                'data' => [
                    'throttle_interval' => 0,
                    'throttle_limit' => 0,
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
