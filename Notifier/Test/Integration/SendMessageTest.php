<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Notifier\Test\Integration;

use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Notifier\Model\SendMessage;
use Magento\Notifier\Test\Integration\Mock\ConfigureMockAdapter;
use PHPUnit\Framework\TestCase;

class SendMessageTest extends TestCase
{
    /**
     * @var SendMessage
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

        $this->subject = $this->objectManager->get(SendMessage::class);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Magento/Notifier/Test/Integration/_files/channels.php
     * @magentoAdminConfigFixture magento_notifier/general/enabled 1
     */
    public function testShouldSendMessage(): void
    {
        $this->assertTrue($this->subject->execute('test_channel_1', 'Title'));
    }

    /**
     * @magentoDataFixture ../../../../app/code/Magento/Notifier/Test/Integration/_files/channels.php
     * @magentoAdminConfigFixture magento_notifier/general/enabled 1
     */
    public function testShouldNotSendMessageWithDisabledChannel(): void
    {
        $this->assertFalse($this->subject->execute('test_disabled_channel', 'Title'));
    }

    /**
     * @magentoDataFixture ../../../../app/code/Magento/Notifier/Test/Integration/_files/channels.php
     * @magentoAdminConfigFixture magento_notifier/general/enabled 0
     */
    public function testShouldNotSendMessageWithDisabledModule(): void
    {
        $this->assertFalse($this->subject->execute('test_channel_1', 'Title'));
    }
}
