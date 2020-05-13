<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Observer;

use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\TwoFactorAuth\Api\UserConfigManagerInterface;

/**
 * Save tfa providers information in user entity
 */
class AdminUserSaveAfter implements ObserverInterface
{
    /**
     * @var UserConfigManagerInterface
     */
    private $userConfigManager;

    /**
     * @var AuthorizationInterface
     */
    private $authorization;

    /**
     * @param UserConfigManagerInterface $userConfigManager
     * @param AuthorizationInterface $authorization
     */
    public function __construct(
        UserConfigManagerInterface $userConfigManager,
        AuthorizationInterface $authorization
    ) {
        $this->userConfigManager = $userConfigManager;
        $this->authorization = $authorization;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        if ($this->authorization->isAllowed('Magento_TwoFactorAuth::tfa')) {
            $user = $observer->getEvent()->getObject();
            $data = $user->getData();

            if (isset($data['tfa_providers'])) {
                if (!is_array($data['tfa_providers'])) {
                    $data['tfa_providers'] = [];
                }
                $this->userConfigManager->setProvidersCodes((int) $user->getId(), $data['tfa_providers']);
            }
        }
    }
}
