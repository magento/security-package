<?php
/**
 * Copyright © MageSpecialist - Skeeller srl. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

use Magento\TestFramework\Helper\Bootstrap;
use Magento\NotifierApi\Model\SerializerInterface;
use Magento\NotifierEvent\Model\GetRulesIdsByEventRegistry;
use Magento\NotifierEvent\Model\Rule;
use Magento\NotifierEventApi\Api\RuleRepositoryInterface;

$objectManager = Bootstrap::getObjectManager();

include __DIR__ . '/../../../../Notifier/Test/Integration/_files/channels.php';

/** @var RuleRepositoryInterface $ruleRepository */
$ruleRepository = $objectManager->get(RuleRepositoryInterface::class);

/** @var SerializerInterface $serializer */
$serializer = $objectManager->get(SerializerInterface::class);

// Create test rules
$testRules = [
    [true, ['test_event_1', 'test_event_2']],
    [true, ['test_event_3']],
    [true, ['test_event_1']],
    [true, ['test_event_2']],
    [false, ['test_event_4']],
];

foreach ($testRules as $testRule) {
    /** @var Rule $rule */
    $rule = $objectManager->create(Rule::class);
    $rule->setEnabled($testRule[0]);
    $rule->setChannelsCodes($serializer->serialize(['test_channel_1']));
    $rule->setName('Test Rule');
    $rule->setTemplateId('*');
    $rule->setThrottleInterval(3600);
    $rule->setThrottleLimit(5);
    $rule->setEvents($serializer->serialize($testRule[1]));

    $ruleRepository->save($rule);
}

/** @var GetRulesIdsByEventRegistry $getRulesIdsByEventRegistry */
$getRulesIdsByEventRegistry = $objectManager->get(GetRulesIdsByEventRegistry::class);
$getRulesIdsByEventRegistry->clearRegistry();
