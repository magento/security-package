<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model\Provider\Engine\U2fKey;

use Magento\Framework\Session\SessionManager;

/**
 * Represents u2f key session data
 *
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 */
class Session extends SessionManager
{
    const CHALLENGE_KEY = 'tfa_u2f_challenge';

    /**
     * Get the current challenge given to the user
     *
     * @return array|null
     */
    public function getU2fChallenge(): ?array
    {
        return $this->storage->getData(static::CHALLENGE_KEY);
    }

    /**
     * Set the current challenge data
     *
     * @param array|null $challenge
     */
    public function setU2fChallenge(?array $challenge): void
    {
        $this->storage->setData(static::CHALLENGE_KEY, $challenge);
    }
}
