<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

use Magento\TestFramework\Helper\Bootstrap;
use Magento\Notifier\Test\Integration\Mock\ConfigureMockAdapter;
use Magento\NotifierTemplate\Model\DatabaseTemplate\Command\SaveInterface;
use Magento\NotifierTemplateApi\Api\Data\DatabaseTemplateInterface;

$objectManager = Bootstrap::getObjectManager();
ConfigureMockAdapter::execute();

/** @var SaveInterface $saveCommand */
$saveCommand = $objectManager->get(SaveInterface::class);

for ($i = 1; $i <= 10; $i++) {
    /** @var DatabaseTemplateInterface $dbTemplate */
    $dbTemplate = $objectManager->create(DatabaseTemplateInterface::class);
    $dbTemplate->setCode('test_template_' . $i);
    $dbTemplate->setName('Test Template ' . $i);
    $dbTemplate->setTemplate('Lorem Ipsum ' . $i);
    $dbTemplate->setAdapterCode('fake');

    $saveCommand->execute($dbTemplate);
}

for ($i = 1; $i <= 10; $i++) {
    /** @var DatabaseTemplateInterface $dbTemplate */
    $dbTemplate = $objectManager->create(DatabaseTemplateInterface::class);
    $dbTemplate->setCode('test_generic_template_' . $i);
    $dbTemplate->setName('Test Template ' . $i);
    $dbTemplate->setTemplate('Lorem Ipsum ' . $i);
    $dbTemplate->setAdapterCode('');

    $saveCommand->execute($dbTemplate);
}
