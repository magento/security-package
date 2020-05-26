<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model\Data;

use Magento\Framework\Api\AbstractExtensibleObject;
use Magento\TwoFactorAuth\Api\Data\AdminTokenResponseInterface;
use Magento\TwoFactorAuth\Api\Data\AdminTokenResponseExtensionInterface;

/**
 * @inheritDoc
 */
class AdminTokenResponse extends AbstractExtensibleObject implements AdminTokenResponseInterface
{
    /**
     * @inheritDoc
     */
    public function getUserId(): string
    {
        return (string)$this->_get(self::USER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setUserId(int $value): void
    {
        $this->setData(self::USER_ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function getMessage(): string
    {
        return (string)$this->_get(self::MESSAGE);
    }

    /**
     * @inheritDoc
     */
    public function setMessage(string $value): void
    {
        $this->setData(self::MESSAGE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getActiveProviders(): array
    {
        return $this->_get(self::ACTIVE_PROVIDERS);
    }

    /**
     * @inheritDoc
     */
    public function setActiveProviders(array $value): void
    {
        $this->setData(self::ACTIVE_PROVIDERS, $value);
    }

    /**
     * @inheritdoc
     */
    public function getExtensionAttributes(): ?AdminTokenResponseExtensionInterface
    {
        return $this->_get(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * @inheritDoc
     */
    public function setExtensionAttributes(AdminTokenResponseExtensionInterface $extensionAttributes): void
    {
        $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}
