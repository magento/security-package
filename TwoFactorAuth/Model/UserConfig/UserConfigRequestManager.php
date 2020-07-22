<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model\UserConfig;

use Magento\Framework\Exception\AuthorizationException;
use Magento\User\Model\User;
use Magento\TwoFactorAuth\Api\TfaInterface;
use Magento\TwoFactorAuth\Api\UserConfigRequestManagerInterface;
use Magento\TwoFactorAuth\Api\UserConfigTokenManagerInterface;
use Magento\TwoFactorAuth\Api\UserNotifierInterface;
use Magento\Framework\Authorization\PolicyInterface as Authorization;

/**
 * @inheritDoc
 */
class UserConfigRequestManager implements UserConfigRequestManagerInterface
{
    /**
     * @var TfaInterface
     */
    private $tfa;

    /**
     * @var UserNotifierInterface
     */
    private $notifier;

    /**
     * @var UserConfigTokenManagerInterface
     */
    private $tokenManager;

    /**
     * @var Authorization
     */
    private $auth;

    /**
     * @param TfaInterface $tfa
     * @param UserNotifierInterface $notifier
     * @param UserConfigTokenManagerInterface $tokenManager
     * @param Authorization $auth
     */
    public function __construct(
        TfaInterface $tfa,
        UserNotifierInterface $notifier,
        UserConfigTokenManagerInterface $tokenManager,
        Authorization $auth
    ) {
        $this->tfa = $tfa;
        $this->notifier = $notifier;
        $this->tokenManager = $tokenManager;
        $this->auth = $auth;
    }

    /**
     * @inheritDoc
     */
    public function isConfigurationRequiredFor(int $userId): bool
    {
        return empty($this->tfa->getUserProviders($userId))
            || !empty($this->tfa->getProvidersToActivate($userId));
    }

    /**
     * @inheritDoc
     */
    public function sendConfigRequestTo(User $user): void
    {
        $userId = (int)$user->getId();
        if (empty($this->tfa->getUserProviders($userId))) {
            //Application level configuration is required.
            if (!$this->auth->isAllowed($user->getAclRole(), 'Magento_TwoFactorAuth::config')) {
                throw new AuthorizationException(__('User is not authorized to edit 2FA configuration'));
            }
            $this->notifier->sendAppConfigRequestMessage($user, $this->tokenManager->issueFor($userId));
        } else {
            //Personal provider config required.
            $this->notifier->sendUserConfigRequestMessage($user, $this->tokenManager->issueFor($userId));
        }
    }
}
