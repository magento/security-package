<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\NotifierAsync\Test\Integration;

use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use MSP\NotifierApi\Api\ChannelRepositoryInterface;
use PHPUnit\Framework\TestCase;

class ExtensionAttributesTest extends TestCase
{
    /**
     * @var ChannelRepositoryInterface
     */
    private $channelRepository;

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
        $this->channelRepository = $this->objectManager->get(ChannelRepositoryInterface::class);
    }

    /**
     * @magentoDataFixture ../../../../app/code/MSP/Notifier/Test/Integration/_files/channels.php
     */
    public function testShouldPersistExtensionAttributes(): void
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $channel = $this->channelRepository->getByCode('test_channel_1');
        $channel->getExtensionAttributes()->setSendAsync(true);
        $this->channelRepository->save($channel);

        /** @noinspection PhpUnhandledExceptionInspection */
        $channel = $this->channelRepository->getByCode('test_channel_1');
        $this->assertTrue($channel->getExtensionAttributes()->getSendAsync());
    }

    /**
     * @magentoDataFixture ../../../../app/code/MSP/Notifier/Test/Integration/_files/channels.php
     */
    public function testShouldDefaultExtensionAttributesValues(): void
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $channel = $this->channelRepository->getByCode('test_channel_1');

        $this->assertFalse($channel->getExtensionAttributes()->getSendAsync());
    }
}
