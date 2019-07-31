<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */
/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection PhpDocMissingThrowsInspection */

declare(strict_types=1);

namespace MSP\NotifierTemplate\Test\Integration;

use Magento\TestFramework\Helper\Bootstrap;
use MSP\Notifier\Test\Integration\Mock\ConfigureMockAdapter;
use MSP\NotifierTemplate\Model\TemplateGetter\TemplateGetter;
use MSP\NotifierTemplate\Test\Integration\Mock\ConfigureMockFilesystemTemplates;
use PHPUnit\Framework\TestCase;

class TemplateGetterTest extends TestCase
{
    /**
     * @var TemplateGetter
     */
    private $templateGetter;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        ConfigureMockAdapter::execute();
        ConfigureMockFilesystemTemplates::execute();
        $this->templateGetter = Bootstrap::getObjectManager()->get(TemplateGetter::class);
    }

    /**
     * @magentoDataFixture ../../../../app/code/MSP/Notifier/Test/Integration/_files/channels.php
     * @magentoDataFixture ../../../../app/code/MSP/NotifierTemplate/Test/Integration/_files/db_templates.php
     */
    public function testTemplateFromDb(): void
    {
        $text = $this->templateGetter->getTemplate('test_channel_1', 'test_template_4');
        $this->assertSame('Lorem Ipsum 4', $text);
    }

    /**
     * @magentoDataFixture ../../../../app/code/MSP/Notifier/Test/Integration/_files/channels.php
     */
    public function testTemplateFromFilesystem(): void
    {
        $text = $this->templateGetter->getTemplate('test_channel_1', 'test_template_1');
        $this->assertSame('This is a test template message #1', $text);
    }

    /**
     * @magentoDataFixture ../../../../app/code/MSP/Notifier/Test/Integration/_files/channels.php
     */
    public function testAdapterSpecificTemplateFromFilesystem(): void
    {
        $text = $this->templateGetter->getTemplate('test_channel_2', 'test_template_2');
        $this->assertSame('This is an adapter specific test template message #2', $text);
    }

    /**
     * @magentoDataFixture ../../../../app/code/MSP/Notifier/Test/Integration/_files/channels.php
     * @magentoDataFixture ../../../../app/code/MSP/NotifierTemplate/Test/Integration/_files/db_templates.php
     */
    public function testShouldPickTemplateFromDatabaseFirst(): void
    {
        $text = $this->templateGetter->getTemplate('test_channel_1', 'test_template_1');
        $this->assertSame('Lorem Ipsum 1', $text);
    }
}
