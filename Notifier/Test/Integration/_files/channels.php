<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

use Magento\TestFramework\Helper\Bootstrap;
use MSP\NotifierApi\Api\ChannelRepositoryInterface;
use MSP\NotifierApi\Api\Data\ChannelInterface;
use MSP\Notifier\Test\Integration\Mock\ConfigureMockAdapter;

$objectManager = Bootstrap::getObjectManager();
ConfigureMockAdapter::execute();

/** @var ChannelRepositoryInterface $repo */
$repo = $objectManager->get(ChannelRepositoryInterface::class);

for ($i = 1; $i <= 10; $i++) {
    /** @var ChannelInterface $channel */
    $channel = $objectManager->create(ChannelInterface::class);
    $channel->setCode('test_channel_' . $i);
    $channel->setName('Test Channel ' . $i);
    $channel->setAdapterCode('fake');
    $channel->setEnabled(true);
    $channel->setConfigurationJson('{"param1": "a", "param2": "b"}');

    $repo->save($channel);
}

/** @var ChannelInterface $channel */
$channel = $objectManager->create(ChannelInterface::class);
$channel->setCode('test_disabled_channel');
$channel->setName('Test Disabled Channel');
$channel->setAdapterCode('fake');
$channel->setEnabled(false);
$channel->setConfigurationJson('{"param1": "a", "param2": "b"}');

$repo->save($channel);
