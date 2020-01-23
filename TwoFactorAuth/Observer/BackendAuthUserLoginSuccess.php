<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\TwoFactorAuth\Api\TfaInterface;
use Magento\TwoFactorAuth\Api\TrustedManagerInterface;

/**
 * Handle rotation of 2fa token on successful login
 */
class BackendAuthUserLoginSuccess implements ObserverInterface
{
    /**
     * @var TrustedManagerInterface
     */
    private $trustedManager;

    /**
     * @var TfaInterface
     */
    private $tfa;

    public function __construct(
        TfaInterface $tfa,
        TrustedManagerInterface $trustedManager
    ) {
        $this->trustedManager = $trustedManager;
        $this->tfa = $tfa;
    }

    /**
     * @param Observer $observer
     * @return void
     * @SuppressWarnings("PHPMD.UnusedFormalParameter")
     */
    public function execute(Observer $observer)
    {
        if (!$this->tfa->isEnabled()) {
            return;
        }

        if ($this->trustedManager->isTrustedDevice()) {
            $this->trustedManager->rotateTrustedDeviceToken();
        }
    }
}
