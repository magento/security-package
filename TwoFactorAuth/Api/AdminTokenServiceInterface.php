<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Api;

use Magento\TwoFactorAuth\Api\Data\AdminTokenResponseInterface;

/**
 * Obtain basic information about the user required to setup or use 2fa
 */
interface AdminTokenServiceInterface
{
    /**
     * Create access token for admin given the admin credentials.
     *
     * @param string $username
     * @param string $password
     * @throws \Magento\Framework\Exception\InputException For invalid input
     * @throws \Magento\Framework\Exception\AuthenticationException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return AdminTokenResponseInterface
     */
    public function createAdminAccessToken(string $username, string $password): AdminTokenResponseInterface;
}
