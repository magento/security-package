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

    private const TFA_EMAIL_SENT = 'tfa_email_sent';

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

    /**
     * Get flag that tfa configuration email was sent
     *
     * @return bool
     */
    public function isTfaEmailSent(): bool
    {
        return (bool) $this->storage->getData(self::TFA_EMAIL_SENT);
    }

    /**
     * Set flag that tfa configuration email was sent
     */
    public function setTfaEmailSentFlag(): void
    {
        $this->storage->setData(self::TFA_EMAIL_SENT, true);
    }
}
