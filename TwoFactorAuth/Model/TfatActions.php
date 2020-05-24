<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model;

use Magento\Framework\Exception\AuthorizationException;
use Magento\Framework\Serialize\Serializer\Json;
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
     * @var Json
     */
    private $json;

    /**
     * @param UserConfigTokenManagerInterface $tokenManager
     * @param TfaInterface $tfa
     * @param Json $json
     */
    public function __construct(
        UserConfigTokenManagerInterface $tokenManager,
        TfaInterface $tfa,
        Json $json
    ) {
        $this->tokenManager = $tokenManager;
        $this->tfa = $tfa;
        $this->json = $json;
    }

    /**
     * @inheritDoc
     */
    public function getUserProviders(string $tfaToken): array
    {
        $userId = $this->validateTfat($tfaToken);

        return $this->tfa->getUserProviders($userId);
    }

    /**
     * @inheritDoc
     */
    public function getProvidersToActivate(string $tfaToken): array
    {
        $userId = $this->validateTfat($tfaToken);

        return $this->tfa->getProvidersToActivate($userId);
    }

    /**
     * Validate the given 2fa token
     *
     * @param string $tfat
     * @return int
     * @throws AuthorizationException
     */
    private function validateTfat(string $tfat): int
    {
        // phpcs:ignore Magento2.Functions.DiscouragedFunction
        ['user_id' => $userId] = $this->json->unserialize(explode('.', base64_decode($tfat))[0]);
        if (!$this->tokenManager->isValidFor($userId, $tfat)) {
            throw new AuthorizationException(__('Invalid token.'));
        }

        return $userId;
    }
}
