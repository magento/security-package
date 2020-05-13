<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model\Data\Provider\Engine\Authy;

use Magento\Framework\Model\AbstractExtensibleModel;
use Magento\TwoFactorAuth\Api\Data\AuthyDeviceExtensionInterface;
use Magento\TwoFactorAuth\Api\Data\AuthyDeviceInterface;

/**
 * Represents a device to be verified
 */
class Device extends AbstractExtensibleModel implements AuthyDeviceInterface
{
    /**
     * @inheritDoc
     */
    public function getCountry(): string
    {
        return (string)$this->getData(self::COUNTRY);
    }

    /**
     * @inheritDoc
     */
    public function setCountry(string $value): void
    {
        $this->setData(self::COUNTRY, $value);
    }

    /**
     * @inheritDoc
     */
    public function getPhoneNumber(): string
    {
        return $this->getData(self::PHONE);
    }

    /**
     * @inheritDoc
     */
    public function setPhoneNumber(string $value): void
    {
        $this->setData(self::PHONE, $value);
    }

    /**
     * @inheritDoc
     */
    public function getMethod(): string
    {
        return $this->getData(self::METHOD);
    }

    /**
     * @inheritDoc
     */
    public function setMethod(string $value): void
    {
        $this->setData(self::METHOD, $value);
    }

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * Used fully qualified namespaces in annotations for proper work of extension interface/class code generation
     *
     * @return \Magento\TwoFactorAuth\Api\Data\AuthyDeviceExtensionInterface|null
     */
    public function getExtensionAttributes(): ?AuthyDeviceExtensionInterface
    {
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * Set an extension attributes object
     *
     * @param \Magento\TwoFactorAuth\Api\Data\AuthyDeviceExtensionInterface $extensionAttributes
     * @return void
     */
    public function setExtensionAttributes(AuthyDeviceExtensionInterface $extensionAttributes): void
    {
        $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}
