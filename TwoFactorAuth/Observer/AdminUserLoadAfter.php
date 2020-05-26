<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\TwoFactorAuth\Api\UserConfigManagerInterface;

/**
 * Add tfa providers information to user entity
 */
class AdminUserLoadAfter implements ObserverInterface
{
    /**
     * @var UserConfigManagerInterface
     */
    private $userConfigManager;

    /**
     * @param UserConfigManagerInterface $userConfigManager
     */
    public function __construct(
        UserConfigManagerInterface $userConfigManager
    ) {
        $this->userConfigManager = $userConfigManager;
    }

    /**
     * @inheritDoc
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $user = $observer->getEvent()->getObject();
        $user->setData('tfa_providers', $this->userConfigManager->getProvidersCodes((int) $user->getId()));
    }
}
