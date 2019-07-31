<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

use Magento\TestFramework\Helper\Bootstrap;
use MSP\NotifierApi\Api\ChannelRepositoryInterface;

$objectManager = Bootstrap::getObjectManager();

/** @var ChannelRepositoryInterface $repo */
$repo = $objectManager->get(ChannelRepositoryInterface::class);
$channels = $repo->getList()->getItems();

foreach ($channels as $channel) {
    $repo->deleteById($channel->getId());
}
