<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\TestFramework\Plugin;

use Closure;
use Magento\Backend\App\AbstractAction;
use Magento\Framework\Event\Observer;
use Magento\TestFramework\Request;
use Magento\TwoFactorAuth\Observer\ControllerActionPredispatch;

/**
 * Plugin that allows integration controller tests that are not aware of 2FA to run.
 */
class BypassTwoFactorAuth
{
    public function aroundExecute(
        ControllerActionPredispatch $subject,
        Closure $proceed,
        Observer $observer
    ) : void {
        /** @var $controllerAction AbstractAction */
        $controllerAction = $observer->getEvent()->getData('controller_action');
        if (method_exists($controllerAction, 'getRequest')
            && $controllerAction->getRequest() instanceof Request
            && !$controllerAction->getRequest()->getParam('tfa_enabled')
        ) {
            //Hack that allows integration controller tests that are not aware of 2FA to run
            return;
        }

        $proceed($observer);
    }
}
