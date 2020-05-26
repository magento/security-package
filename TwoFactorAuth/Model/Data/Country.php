<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model\Data;

use Magento\Framework\Model\AbstractExtensibleModel;
use Magento\TwoFactorAuth\Api\Data\CountryExtensionInterface;
use Magento\TwoFactorAuth\Api\Data\CountryInterface;

/**
 * @inheritDoc
 */
class Country extends AbstractExtensibleModel implements CountryInterface
{
    /**
     * @inheritdoc
     */
    public function getId(): int
    {
        return (int) $this->getData(self::ID);
    }

    /**
     * @inheritdoc
     */
    public function setId($value): void
    {
        $this->setData(self::ID, $value);
    }

    /**
     * @inheritdoc
     */
    public function getCode(): string
    {
        return (string) $this->getData(self::CODE);
    }

    /**
     * @inheritdoc
     */
    public function setCode(string $value): void
    {
        $this->setData(self::CODE, $value);
    }

    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return (string) $this->getData(self::NAME);
    }

    /**
     * @inheritdoc
     */
    public function setName(string $value): void
    {
        $this->setData(self::NAME, $value);
    }

    /**
     * @inheritdoc
     */
    public function getDialCode(): string
    {
        return (string) $this->getData(self::DIAL_CODE);
    }

    /**
     * @inheritdoc
     */
    public function setDialCode(string $value): void
    {
        $this->setData(self::DIAL_CODE, $value);
    }

    /**
     * @inheritdoc
     */
    public function getExtensionAttributes(): ?CountryExtensionInterface
    {
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * @inheritdoc
     */
    public function setExtensionAttributes(CountryExtensionInterface $extensionAttributes): void
    {
        $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}
