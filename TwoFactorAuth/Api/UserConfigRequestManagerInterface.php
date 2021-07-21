<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Api;

use Magento\Framework\Exception\AuthorizationException;
use Magento\User\Model\User;
use Magento\TwoFactorAuth\Api\Exception\NotificationExceptionInterface;

/**
 * Manages configuration requests for users.
 *
 * @api
 */
interface UserConfigRequestManagerInterface
{
    /**
     * Is user required to configure 2FA?
     *
     * @param int $userId
     * @return bool
     */
    public function isConfigurationRequiredFor(int $userId): bool;

    /**
     * Request configurations from the user.
     *
     * @param User $user
     * @return void
     * @throws AuthorizationException When user is not allowed to configure 2FA.
     * @throws NotificationExceptionInterface When failed to send the message.
     */
    public function sendConfigRequestTo(User $user): void;
}
