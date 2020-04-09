<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Api;

use Magento\Framework\Webapi\Exception as WebApiException;

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
     * @return void
     * @throws WebApiException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\AuthenticationException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createAdminAccessToken(string $username, string $password): void;
}
