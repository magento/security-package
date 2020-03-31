<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Api;

/**
 * Represents google authentication
 */
interface GoogleAuthenticateInterface
{
    /**
     * Get an admin token by authenticating using google
     *
     * @param int $userId
     * @param Data\GoogleAuthenticateInterface $data
     * @return string
     */
    public function getToken(int $userId, Data\GoogleAuthenticateInterface $data): string;
}
