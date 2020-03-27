<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model;

use Magento\Framework\Exception\AuthorizationException;
use Magento\TwoFactorAuth\Api\TfaInterface;
use Magento\TwoFactorAuth\Api\TfatActionsInterface;
use Magento\TwoFactorAuth\Api\UserConfigTokenManagerInterface;

/**
 * @inheritDoc
 */
class TfatActions implements TfatActionsInterface
{
    /**
     * @var UserConfigTokenManagerInterface
     */
    private $tokenManager;

    /**
     * @var TfaInterface
     */
    private $tfa;

    /**
     * @param UserConfigTokenManagerInterface $tokenManager
     * @param TfaInterface $tfa
     */
    public function __construct(
        UserConfigTokenManagerInterface $tokenManager,
        TfaInterface $tfa
    ) {
        $this->tokenManager = $tokenManager;
        $this->tfa = $tfa;
    }

    /**
     * Get list of providers available for the user
     *
     * @param int $userId
     * @param string $tfaToken
     * @return \Magento\TwoFactorAuth\Api\ProviderInterface[]
     * @throws AuthorizationException
     */
    public function getUserProviders(int $userId, string $tfaToken): array
    {
        $this->validateTfat($userId, $tfaToken);

        return $this->tfa->getUserProviders($userId);
    }

    /**
     * Get list of providers requiring activation
     *
     * @param int $userId
     * @param string $tfaToken
     * @return \Magento\TwoFactorAuth\Api\ProviderInterface[]
     * @throws AuthorizationException
     */
    public function getProvidersToActivate(int $userId, string $tfaToken): array
    {
        $this->validateTfat($userId, $tfaToken);

        return $this->tfa->getProvidersToActivate($userId);
    }

    /**
     * Validate the given 2fa token
     *
     * @param int $userId
     * @param string $tfat
     * @throws AuthorizationException
     */
    private function validateTfat(int $userId, string $tfat): void
    {
        if (!$this->tokenManager->isValidFor($userId, $tfat)) {
            throw new AuthorizationException(__('Invalid token.'));
        }
    }
}
