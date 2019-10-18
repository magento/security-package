<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocMissingThrowsInspection */

declare(strict_types=1);

namespace Magento\NotifierEvent\Test\Integration;

use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\NotifierEvent\Model\GetAutomaticTemplateId;
use Magento\NotifierEvent\Model\IsEventExcludedRegex;
use PHPUnit\Framework\TestCase;

class IsEventExcludedRegexTest extends TestCase
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var GetAutomaticTemplateId
     */
    private $subject;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        $this->objectManager = Bootstrap::getObjectManager();
    }

    /**
     * @return array
     */
    public function nonExcludedDataProvider(): array
    {
        return [
            [
                'eventNames' => [
                    'some_load_event',
                    'some_other_event'
                ],
                'skipEvents' => [
                    '/_(load|save)_(after|before)$/'
                ]
            ]
        ];
    }

    /**
     * @return array
     */
    public function excludedDataProvider(): array
    {
        return [
            [
                'eventNames' => [
                    'model_load_before',
                    'model_load_after',
                    'model_save_before',
                    'model_save_after',
                ],
                'skipEvents' => [
                    '/_(load|save)_(after|before)$/'
                ]
            ]
        ];
    }

    /**
     * @param array $eventNames
     * @param array $skipEvents
     * @dataProvider excludedDataProvider
     */
    public function testShouldExcludeRegex(array $eventNames, array $skipEvents): void
    {
        /** @var IsEventExcludedRegex $isEventExcludedRegex */
        $isEventExcludedRegex = $this->objectManager->create(
            IsEventExcludedRegex::class,
            [
                'skipEvents' => $skipEvents
            ]
        );

        foreach ($eventNames as $eventName) {
            $this->assertTrue($isEventExcludedRegex->execute($eventName));
        }
    }

    /**
     * @param array $eventNames
     * @param array $skipEvents
     * @dataProvider nonExcludedDataProvider
     */
    public function testShouldNotExcludeAllowedEvents(array $eventNames, array $skipEvents): void
    {
        /** @var IsEventExcludedRegex $isEventExcludedRegex */
        $isEventExcludedRegex = $this->objectManager->create(
            IsEventExcludedRegex::class,
            [
                'skipEvents' => $skipEvents
            ]
        );

        foreach ($eventNames as $eventName) {
            $this->assertFalse($isEventExcludedRegex->execute($eventName));
        }
    }
}
