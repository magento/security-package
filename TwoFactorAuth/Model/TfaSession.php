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
 *
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 */
class TfaSession extends SessionManager implements TfaSessionInterface
{
    const SKIPPED_PROVIDERS_KEY = 'tfa_skipped_config';

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

    /**
     * @inheritDoc
     *
     * @return array
     */
    public function getSkippedProviderConfig(): array
    {
        return $this->getData(static::SKIPPED_PROVIDERS_KEY) ?? [];
    }

    /**
     * @inheritDoc
     */
    public function setSkippedProviderConfig(array $config): void
    {
        $this->storage->setData(static::SKIPPED_PROVIDERS_KEY, $config);
    }
}
