<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\NotifierTemplate\Test\Integration;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Notifier\Test\Integration\Mock\ConfigureMockAdapter;
use Magento\NotifierTemplate\Model\DatabaseTemplate;
use Magento\NotifierTemplate\Model\DatabaseTemplateRepository;
use PHPUnit\Framework\TestCase;

class DatabaseTemplateRepositoryTest extends TestCase
{
    /**
     * @var DatabaseTemplateRepository
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
        ConfigureMockAdapter::execute();

        $this->subject = $this->objectManager->get(DatabaseTemplateRepository::class);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Magento/NotifierTemplate/Test/Integration/_files/db_templates.php
     */
    public function testShouldGetList(): void
    {
        $list = $this->subject->getList();

        $this->assertSame(20, $list->getTotalCount());
        $codes = [];
        foreach ($list->getItems() as $databaseTemplate) {
            $codes[] = $databaseTemplate->getCode();
        }

        $this->assertEquals(
            [
                'test_template_1',
                'test_template_2',
                'test_template_3',
                'test_template_4',
                'test_template_5',
                'test_template_6',
                'test_template_7',
                'test_template_8',
                'test_template_9',
                'test_template_10',
                'test_generic_template_1',
                'test_generic_template_2',
                'test_generic_template_3',
                'test_generic_template_4',
                'test_generic_template_5',
                'test_generic_template_6',
                'test_generic_template_7',
                'test_generic_template_8',
                'test_generic_template_9',
                'test_generic_template_10',
            ],
            $codes
        );
    }

    /**
     * @magentoDataFixture ../../../../app/code/Magento/NotifierTemplate/Test/Integration/_files/db_templates.php
     */
    public function testShouldGetByCode(): void
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $databaseTemplate = $this->subject->getByCode('test_template_4');
        $this->assertSame('test_template_4', $databaseTemplate->getCode());
        $this->assertSame('Test Template 4', $databaseTemplate->getName());
    }

    /**
     * @magentoDataFixture ../../../../app/code/Magento/NotifierTemplate/Test/Integration/_files/db_templates.php
     */
    public function testShouldTriggerExceptionWhenCodeIsNotFound(): void
    {
        $this->expectException(NoSuchEntityException::class);

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->subject->getByCode('non_existing_database_template');
    }

    /**
     * @magentoDataFixture ../../../../app/code/Magento/NotifierTemplate/Test/Integration/_files/db_templates.php
     */
    public function testShouldDelete(): void
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $databaseTemplate = $this->subject->getByCode('test_template_4');
        $this->subject->deleteById((int) $databaseTemplate->getId());

        $list = $this->subject->getList();
        $this->assertSame(19, $list->getTotalCount());

        $this->expectException(NoSuchEntityException::class);

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->subject->getByCode('test_template_4');
    }

    /**
     * @magentoDataFixture ../../../../app/code/Magento/NotifierTemplate/Test/Integration/_files/db_templates.php
     */
    public function testShouldSave(): void
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $databaseTemplate = $this->subject->getByCode('test_template_4');
        $databaseTemplate->setName('New name');
        $this->subject->save($databaseTemplate);

        /** @noinspection PhpUnhandledExceptionInspection */
        $databaseTemplate = $this->subject->getByCode('test_template_4');
        $this->assertSame('New name', $databaseTemplate->getName());

        // Make sure a new databaseTemplate was not created
        $list = $this->subject->getList();
        $this->assertSame(20, $list->getTotalCount());
    }

    /**
     * @magentoDataFixture ../../../../app/code/Magento/NotifierTemplate/Test/Integration/_files/db_templates.php
     */
    public function testShouldCreate(): void
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $databaseTemplate = $this->objectManager->create(DatabaseTemplate::class);
        $databaseTemplate->setCode('new_databaseTemplate');
        $databaseTemplate->setName('New Channel');
        $databaseTemplate->setAdapterCode('fake');
        $databaseTemplate->setTemplate('Lorem Ipsum');
        $this->subject->save($databaseTemplate);

        $list = $this->subject->getList();
        $this->assertSame(21, $list->getTotalCount());

        /** @noinspection PhpUnhandledExceptionInspection */
        $databaseTemplate = $this->subject->getByCode('new_databaseTemplate');
        $this->assertSame('New Channel', $databaseTemplate->getName());
    }
}
