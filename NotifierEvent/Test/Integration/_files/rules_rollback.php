<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

use Magento\TestFramework\Helper\Bootstrap;
use Magento\NotifierEventApi\Api\RuleRepositoryInterface;

$objectManager = Bootstrap::getObjectManager();

/** @var RuleRepositoryInterface $ruleRepository */
$ruleRepository = $objectManager->get(RuleRepositoryInterface::class);

$rules = $ruleRepository->getList()->getItems();
foreach ($rules as $rule) {
    $ruleRepository->deleteById((int) $rule->getId());
}

include __DIR__ . '/../../../../Notifier/Test/Integration/_files/channels_rollback.php';
