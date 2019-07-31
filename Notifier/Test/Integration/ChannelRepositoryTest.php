<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace MSP\Notifier\Test\Integration;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use MSP\Notifier\Model\Channel;
use MSP\Notifier\Model\ChannelRepository;
use MSP\Notifier\Test\Integration\Mock\ConfigureMockAdapter;
use PHPUnit\Framework\TestCase;

class ChannelRepositoryTest extends TestCase
{
    /**
     * @var ChannelRepository
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

        $this->subject = $this->objectManager->get(ChannelRepository::class);
    }

    /**
     * @magentoDataFixture ../../../../app/code/MSP/Notifier/Test/Integration/_files/channels.php
     */
    public function testShouldGetList(): void
    {
        $list = $this->subject->getList();

        $this->assertSame(11, $list->getTotalCount());
        $codes = [];
        foreach ($list->getItems() as $channel) {
            $codes[] = $channel->getCode();
        }

        $this->assertEquals(
            [
                'test_channel_1',
                'test_channel_2',
                'test_channel_3',
                'test_channel_4',
                'test_channel_5',
                'test_channel_6',
                'test_channel_7',
                'test_channel_8',
                'test_channel_9',
                'test_channel_10',
                'test_disabled_channel',
            ],
            $codes
        );
    }

    /**
     * @magentoDataFixture ../../../../app/code/MSP/Notifier/Test/Integration/_files/channels.php
     */
    public function testShouldGetByCode(): void
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $channel = $this->subject->getByCode('test_channel_4');
        $this->assertSame('test_channel_4', $channel->getCode());
        $this->assertSame('Test Channel 4', $channel->getName());
    }

    /**
     * @magentoDataFixture ../../../../app/code/MSP/Notifier/Test/Integration/_files/channels.php
     */
    public function testShouldTriggerExceptionWhenCodeIsNotFound(): void
    {
        $this->expectException(NoSuchEntityException::class);

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->subject->getByCode('non_existing_channel');
    }

    /**
     * @magentoDataFixture ../../../../app/code/MSP/Notifier/Test/Integration/_files/channels.php
     */
    public function testShouldDelete(): void
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $channel = $this->subject->getByCode('test_channel_4');
        $this->subject->deleteById((int) $channel->getId());

        $list = $this->subject->getList();
        $this->assertSame(10, $list->getTotalCount());

        $this->expectException(NoSuchEntityException::class);

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->subject->getByCode('test_channel_4');
    }

    /**
     * @magentoDataFixture ../../../../app/code/MSP/Notifier/Test/Integration/_files/channels.php
     */
    public function testShouldSave(): void
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $channel = $this->subject->getByCode('test_channel_4');
        $channel->setName('New name');
        $this->subject->save($channel);

        /** @noinspection PhpUnhandledExceptionInspection */
        $channel = $this->subject->getByCode('test_channel_4');
        $this->assertSame('New name', $channel->getName());

        // Make sure a new channel was not created
        $list = $this->subject->getList();
        $this->assertSame(11, $list->getTotalCount());
    }

    /**
     * @magentoDataFixture ../../../../app/code/MSP/Notifier/Test/Integration/_files/channels.php
     */
    public function testShouldCreate(): void
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $channel = $this->objectManager->create(Channel::class);
        $channel->setCode('new_channel');
        $channel->setName('New Channel');
        $channel->setAdapterCode('fake');
        $channel->setConfigurationJson('{"param1": "a", "param2": "b"}');
        $this->subject->save($channel);

        $list = $this->subject->getList();
        $this->assertSame(12, $list->getTotalCount());

        /** @noinspection PhpUnhandledExceptionInspection */
        $channel = $this->subject->getByCode('new_channel');
        $this->assertSame('New Channel', $channel->getName());
    }
}
