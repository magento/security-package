<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\TestFramework\Helper\Bootstrap;

/**
 * Delete dummy user
 */
$objectManager = Bootstrap::getObjectManager();
/** @var Registry $registry */
$registry = $objectManager->get(Registry::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

try {
    $user = $objectManager->create(User::class);
    $user->load('dummy_username', 'username');
    $user->delete();
} catch (NoSuchEntityException $e) {
    //already removed
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
