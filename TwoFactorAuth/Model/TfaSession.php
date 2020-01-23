<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model;

use Magento\Framework\Session\SessionManager;
use Magento\TwoFactorAuth\Api\TfaSessionInterface;

/**
 * @inheritDoc
 */
class TfaSession extends SessionManager implements TfaSessionInterface
{
    /**
     * @inheritDoc
     */
    public function grantAccess(): void
    {
        $this->storage->setData(TfaSessionInterface::KEY_PASSED, true);
    }

    /**
     * @inheritDoc
     */
    public function isGranted(): bool
    {
        return (bool) $this->storage->getData(TfaSessionInterface::KEY_PASSED);
    }
}
