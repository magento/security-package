<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Plugin;

use Closure;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\TwoFactorAuth\Api\TfaInterface;
use Magento\User\Observer\Backend\ForceAdminPasswordChangeObserver;

/**
 * Avoid a recursion when force password change is activated in backend
 */
class AvoidRecursionOnPasswordChange
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var TfaInterface
     */
    private $tfa;

    /**
     * @param RequestInterface $request
     * @param TfaInterface $tfa
     */
    public function __construct(
        RequestInterface $request,
        TfaInterface $tfa
    ) {
        $this->request = $request;
        $this->tfa = $tfa;
    }

    /**
     * Prevent recursion
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ForceAdminPasswordChangeObserver $subject
     * @param Closure $proceed
     * @param EventObserver $observer
     * @return void
     */
    public function aroundExecute(
        ForceAdminPasswordChangeObserver $subject,
        Closure $proceed,
        EventObserver $observer
    ) {
        /*
         * We need to bypass ForceAdminPasswordChangeObserver::execute while authenticating 2FA
         * to avoid a recursion loop caused by two different redirects
         */
        $fullActionName = $this->request->getFullActionName();
        if (!in_array($fullActionName, $this->tfa->getAllowedUrls(), true)) {
            $proceed($observer);
        }
    }
}
