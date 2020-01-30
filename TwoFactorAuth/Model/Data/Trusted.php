<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model\Data;

use Magento\Framework\Api\AbstractExtensibleObject;
use Magento\TwoFactorAuth\Api\Data\TrustedExtensionInterface;
use Magento\TwoFactorAuth\Api\Data\TrustedInterface;

/**
 * @inheritDoc
 */
class Trusted extends AbstractExtensibleObject implements TrustedInterface
{
    /**
     * @inheritDoc
     */
    public function getId(): int
    {
        return (int) $this->_get(self::ID);
    }

    /**
     * @inheritDoc
     */
    public function setId(int $value): void
    {
        $this->setData(self::ID, $value);
    }

    /**
     * @inheritDoc
     */
    public function getDateTime(): string
    {
        return (string) $this->_get(self::DATE_TIME);
    }

    /**
     * @inheritDoc
     */
    public function setDateTime(string $value): void
    {
        $this->setData(self::DATE_TIME, $value);
    }

    /**
     * @inheritDoc
     */
    public function getUserId(): int
    {
        return (int) $this->_get(self::USER_ID);
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
    public function getDeviceName(): string
    {
        return (string) $this->_get(self::DEVICE_NAME);
    }

    /**
     * @inheritDoc
     */
    public function setDeviceName(string $value): void
    {
        $this->setData(self::DEVICE_NAME, $value);
    }

    /**
     * @inheritDoc
     */
    public function getToken(): string
    {
        return (string) $this->_get(self::TOKEN);
    }

    /**
     * @inheritDoc
     */
    public function setToken(string $value): void
    {
        $this->setData(self::TOKEN, $value);
    }

    /**
     * @inheritDoc
     */
    public function getLastIp(): string
    {
        return (string) $this->_get(self::LAST_IP);
    }

    /**
     * @inheritDoc
     */
    public function setLastIp(string $value): void
    {
        $this->setData(self::LAST_IP, $value);
    }

    /**
     * @inheritDoc
     */
    public function getUserAgent(): string
    {
        return (string) $this->_get(self::USER_AGENT);
    }

    /**
     * @inheritDoc
     */
    public function setUserAgent(string $value): void
    {
        $this->setData(self::USER_AGENT, $value);
    }

    /**
     * @inheritDoc
     */
    public function getExtensionAttributes(): ?TrustedExtensionInterface
    {
        return $this->_get(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * @inheritDoc
     */
    public function setExtensionAttributes(TrustedExtensionInterface $extensionAttributes): void
    {
        $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}
