<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocMissingThrowsInspection */

declare(strict_types=1);

namespace MSP\NotifierEvent\Test\Integration;

use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use MSP\NotifierEvent\Model\GetRulesIdsByEvent;
use PHPUnit\Framework\TestCase;

class GetRuleIdsByEventTest extends TestCase
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var GetRulesIdsByEvent
     */
    private $subject;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->subject = $this->objectManager->get(GetRulesIdsByEvent::class);
    }

    /**
     * @return array
     */
    public function eventNamesAndCountDataProvider(): array
    {
        return [
            [
                'expectedCount' => 2,
                'eventName' => 'test_event_1'
            ],
            [
                'expectedCount' => 2,
                'eventName' => 'test_event_2'
            ],
            [
                'expectedCount' => 1,
                'eventName' => 'test_event_3'
            ],
            [
                'expectedCount' => 0,
                'eventName' => 'test_event_4' // Disabled event
            ],
            [
                'expectedCount' => 0,
                'eventName' => 'unknown_event'
            ]
        ];
    }

    /**
     * @magentoDataFixture ../../../../app/code/MSP/NotifierEvent/Test/Integration/_files/rules.php
     * @dataProvider eventNamesAndCountDataProvider
     * @param int $expectedCount
     * @param string $eventName
     */
    public function testShouldGetTheRightAmountOfRules(int $expectedCount, string $eventName): void
    {
        $this->assertCount($expectedCount, $this->subject->execute($eventName));
    }
}
