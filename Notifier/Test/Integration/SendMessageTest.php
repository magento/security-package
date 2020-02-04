<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Notifier\Test\Integration;

use Magento\Framework\ObjectManagerInterface;
use Magento\Notifier\Model\BuildMessage;
use Magento\Notifier\Model\ChannelRepository;
use Magento\Notifier\Model\SendMessage;
use Magento\Notifier\Test\Integration\Mock\ConfigureMockAdapter;
use Magento\NotifierApi\Exception\NotifierChannelDisabledException;
use Magento\NotifierApi\Exception\NotifierDisabledException;
use Magento\NotifierApi\Model\SerializerInterface;
use Magento\TestFramework\Helper\Bootstrap;
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
     * @var BuildMessage
     */
    private $buildMessage;

    /**
     * @var ChannelRepository
     */
    private $channelRepository;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        $this->objectManager = Bootstrap::getObjectManager();
        ConfigureMockAdapter::execute();
        $this->subject = $this->objectManager->get(SendMessage::class);
        $this->buildMessage = $this->objectManager->get(BuildMessage::class);
        $this->channelRepository = $this->objectManager->get(ChannelRepository::class);
        $this->serializer = $this->objectManager->get(SerializerInterface::class);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Magento/Notifier/Test/Integration/_files/channels.php
     * @magentoAdminConfigFixture magento_notifier/general/enabled 1
     */
    public function testShouldSendMessage(): void
    {
        $channelCode = 'test_channel_1';
        $messageText = 'Title';
        $channel = $this->channelRepository->getByCode($channelCode);
        $params = $this->serializer->unserialize($channel->getConfigurationJson());
        $message = $this->buildMessage->execute($messageText, $params);

        $this->subject->execute($channel, $message);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Magento/Notifier/Test/Integration/_files/channels.php
     * @magentoAdminConfigFixture magento_notifier/general/enabled 1
     */
    public function testShouldNotSendMessageWithDisabledChannel(): void
    {
        $this->expectException(NotifierChannelDisabledException::class);
        $this->expectExceptionMessage('Notifier channel test_disabled_channel is disabled.');

        $channelCode = 'test_disabled_channel';
        $messageText = 'Title';
        $channel = $this->channelRepository->getByCode($channelCode);
        $params = $this->serializer->unserialize($channel->getConfigurationJson());
        $message = $this->buildMessage->execute($messageText, $params);

        $this->subject->execute($channel, $message);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Magento/Notifier/Test/Integration/_files/channels.php
     * @magentoAdminConfigFixture magento_notifier/general/enabled 0
     */
    public function testShouldNotSendMessageWithDisabledModule(): void
    {
        $this->expectException(NotifierDisabledException::class);
        $this->expectExceptionMessage('Notifier service is disabled');

        $channelCode = 'test_channel_1';
        $messageText = 'Title';
        $channel = $this->channelRepository->getByCode($channelCode);
        $params = $this->serializer->unserialize($channel->getConfigurationJson());
        $message = $this->buildMessage->execute($messageText, $params);

        $this->subject->execute($channel, $message);
    }
}
