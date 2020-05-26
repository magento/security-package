<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Api;

use Magento\User\Model\User;
use Magento\TwoFactorAuth\Api\Exception\NotificationExceptionInterface;

/**
 * Sends notifications to users regarding 2FA.
 */
interface UserNotifierInterface
{
    /**
     * URL to be used for communications sent from webapi_* area
     */
    public const XML_PATH_WEBAPI_NOTIFICATION_URL = 'twofactorauth/general/webapi_notification_url';

    /**
     * Send message allowing an admin user to configure personal 2FA.
     *
     * @param User $user
     * @param string $token Token that will allow not fully authenticated user to edit configurations.
     * @return void
     * @throws NotificationExceptionInterface
     */
    public function sendUserConfigRequestMessage(User $user, string $token): void;

    /**
     * Send message requesting 2FA for the whole application to be configured.
     *
     * @param User $user
     * @param string $token Token that will allow not fully authenticated user to edit configurations.
     * @return void
     * @throws NotificationExceptionInterface
     */
    public function sendAppConfigRequestMessage(User $user, string $token): void;
}
