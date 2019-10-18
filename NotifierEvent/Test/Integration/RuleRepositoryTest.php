<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierEvent\Test\Integration;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\NotifierApi\Model\SerializerInterface;
use Magento\NotifierEvent\Model\Rule;
use Magento\NotifierEvent\Model\RuleRepository;
use PHPUnit\Framework\TestCase;

class RuleRepositoryTest extends TestCase
{
    /**
     * @var RuleRepository
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
        $this->subject = $this->objectManager->get(RuleRepository::class);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Magento/NotifierEvent/Test/Integration/_files/rules.php
     */
    public function testShouldGetList(): void
    {
        $list = $this->subject->getList();

        $this->assertSame(5, $list->getTotalCount());
    }

    /**
     * @magentoDataFixture ../../../../app/code/Magento/NotifierEvent/Test/Integration/_files/rules.php
     */
    public function testShouldGetById(): void
    {
        $firstItem = current($this->subject->getList()->getItems());
        
        /** @noinspection PhpUnhandledExceptionInspection */
        $rule = $this->subject->get((int) $firstItem->getId());
        $this->assertSame((int) $firstItem->getId(), (int) $rule->getId());
    }

    /**
     * @magentoDataFixture ../../../../app/code/Magento/NotifierEvent/Test/Integration/_files/rules.php
     */
    public function testShouldTriggerExceptionWhenCodeIsNotFound(): void
    {
        $this->expectException(NoSuchEntityException::class);

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->subject->get(-1);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Magento/NotifierEvent/Test/Integration/_files/rules.php
     */
    public function testShouldDelete(): void
    {
        /** @var Rule $firstItem */
        $firstItem = current($this->subject->getList()->getItems());
        $this->subject->deleteById((int) $firstItem->getId());

        $list = $this->subject->getList();
        $this->assertSame(4, $list->getTotalCount());

        $this->expectException(NoSuchEntityException::class);

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->subject->get((int) $firstItem->getId());
    }

    /**
     * @magentoDataFixture ../../../../app/code/Magento/NotifierEvent/Test/Integration/_files/rules.php
     */
    public function testShouldSave(): void
    {
        /** @var Rule $firstItem */
        $firstItem = current($this->subject->getList()->getItems());

        /** @noinspection PhpUnhandledExceptionInspection */
        $rule = $this->subject->get((int) $firstItem->getId());
        $rule->setName('New name');
        $this->subject->save($rule);

        /** @noinspection PhpUnhandledExceptionInspection */
        $rule = $this->subject->get((int) $firstItem->getId());
        $this->assertSame('New name', $rule->getName());

        // Make sure a new rule was not created
        $list = $this->subject->getList();
        $this->assertSame(5, $list->getTotalCount());
    }

    /**
     * @magentoDataFixture ../../../../app/code/Magento/NotifierEvent/Test/Integration/_files/rules.php
     */
    public function testShouldCreate(): void
    {
        /** @var Rule $rule */
        $rule = $this->objectManager->create(Rule::class);

        /** @var SerializerInterface $serializer */
        $serializer = $this->objectManager->get(SerializerInterface::class);

        $rule->setEnabled(true);
        $rule->setChannelsCodes($serializer->serialize(['test_channel_1']));
        $rule->setName('New Test Rule');
        $rule->setTemplateId('*');
        $rule->setThrottleInterval(3600);
        $rule->setThrottleLimit(5);
        $rule->setEvents($serializer->serialize(['test_event']));
        $newId = $this->subject->save($rule);

        $list = $this->subject->getList();
        $this->assertSame(6, $list->getTotalCount());

        /** @noinspection PhpUnhandledExceptionInspection */
        $rule = $this->subject->get($newId);
        $this->assertSame('New Test Rule', $rule->getName());
    }
}
