<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

use Magento\TestFramework\Helper\Bootstrap;
use Magento\NotifierTemplate\Model\DatabaseTemplate\Command\DeleteInterface;
use Magento\NotifierTemplate\Model\DatabaseTemplate\Command\GetListInterface;

$objectManager = Bootstrap::getObjectManager();

/** @var GetListInterface $getList */
$getList = $objectManager->get(GetListInterface::class);
$databaseTemplates = $getList->execute()->getItems();

/** @var DeleteInterface $deleteCommand */
$deleteCommand = $objectManager->get(DeleteInterface::class);

foreach ($databaseTemplates as $databaseTemplate) {
    $deleteCommand->execute($databaseTemplate->getId());
}
