<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocMissingThrowsInspection */

declare(strict_types=1);

namespace Magento\NotifierEvent\Test\Integration\Rule\Validator;

use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\NotifierEvent\Model\Rule\Validator\ValidateThrottle;
use Magento\NotifierEventApi\Api\Data\RuleInterface;
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
                    RuleInterface::THROTTLE_INTERVAL => -10,
                    RuleInterface::THROTTLE_LIMIT => 0,
                ],
                'errorMessage' => 'Throttle interval must be greater or equal to 0'
            ],
            [
                'data' => [
                    RuleInterface::THROTTLE_INTERVAL => 0,
                    RuleInterface::THROTTLE_LIMIT => -10,
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
                    RuleInterface::THROTTLE_INTERVAL => 10,
                    RuleInterface::THROTTLE_LIMIT => 10,
                ]
            ],
            [
                'data' => [
                    RuleInterface::THROTTLE_INTERVAL => 0,
                    RuleInterface::THROTTLE_LIMIT => 0,
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
