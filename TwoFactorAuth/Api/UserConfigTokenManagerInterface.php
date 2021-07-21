<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Api;

/**
 * Manages tokens issued to users to authorize them to configure 2FA.
 *
 * @api
 */
interface UserConfigTokenManagerInterface
{
    /**
     * Issue token for the user.
     *
     * @param int $userId
     * @return string
     */
    public function issueFor(int $userId): string;

    /**
     * Is given token valid for given user?
     *
     * @param int $userId
     * @param string $token
     * @return bool
     */
    public function isValidFor(int $userId, string $token): bool;
}
