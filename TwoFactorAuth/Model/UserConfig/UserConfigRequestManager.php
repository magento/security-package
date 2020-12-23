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
use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\ObjectManager;

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
     * @var CacheInterface
     */
    private $cache;

    /**
     * @param TfaInterface $tfa
     * @param UserNotifierInterface $notifier
     * @param UserConfigTokenManagerInterface $tokenManager
     * @param Authorization $auth
     * @param CacheInterface|null $cache
     */
    public function __construct(
        TfaInterface $tfa,
        UserNotifierInterface $notifier,
        UserConfigTokenManagerInterface $tokenManager,
        Authorization $auth,
        CacheInterface $cache = null
    ) {
        $this->tfa = $tfa;
        $this->notifier = $notifier;
        $this->tokenManager = $tokenManager;
        $this->auth = $auth;
        $this->cache = $cache ?? ObjectManager::getInstance()->get(CacheInterface::class);
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
            $tfaToken = $this->cache->load(SignedTokenManager::CACHE_ID . $userId);
            $isValidOldToken = false;
            if ($tfaToken !== false) {
                $isValidOldToken = $this->tokenManager->isValidFor($userId, $tfaToken);
            }
            //Application level configuration is required.
            if (!$this->auth->isAllowed($user->getAclRole(), 'Magento_TwoFactorAuth::config')) {
                throw new AuthorizationException(__('User is not authorized to edit 2FA configuration'));
            }
            if (!$isValidOldToken) {
                $this->notifier->sendAppConfigRequestMessage($user, $this->tokenManager->issueFor($userId));
            }
        } else {
            //Personal provider config required.
            $this->notifier->sendUserConfigRequestMessage($user, $this->tokenManager->issueFor($userId));
        }
    }
}
