<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Api;

use Magento\Framework\DataObject;
use Magento\User\Api\Data\UserInterface;

/**
 * 2FA engine interface
 */
interface EngineInterface
{
    /**
     * Return true if this provider has been enabled by admin
     *
     * @return bool
     */
    public function isEnabled(): bool;

    /**
     * Return true on token validation
     *
     * @param UserInterface $user
     * @param DataObject $request
     * @return bool
     */
    public function verify(UserInterface $user, DataObject $request): bool;
}
