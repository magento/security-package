<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model\Data\Provider\Engine\Google;

use Magento\Framework\Model\AbstractExtensibleModel;
use Magento\TwoFactorAuth\Api\Data\GoogleConfigureExtensionInterface;
use Magento\TwoFactorAuth\Api\Data\GoogleConfigureInterface;

/**
 * Represents google configuration data
 */
class ConfigurationData extends AbstractExtensibleModel implements GoogleConfigureInterface
{
    /**
     * Get value for QR code base 64
     *
     * @return string
     */
    public function getQrCodeBase64(): string
    {
        return (string)$this->getData(self::QR_CODE_BASE64);
    }

    /**
     * Set value for QR code base 64
     *
     * @param string $value
     * @return void
     */
    public function setQrCodeBase64(string $value): void
    {
        $this->setData(self::QR_CODE_BASE64, $value);
    }

    /**
     * Get value for secret code
     *
     * @return string
     */
    public function getSecretCode(): string
    {
        return (string)$this->getData(self::SECRET_CODE);
    }

    /**
     * Set value for secret code
     *
     * @param string $value
     * @return void
     */
    public function setSecretCode(string $value): void
    {
        $this->setData(self::SECRET_CODE, $value);
    }

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * Used fully qualified namespaces in annotations for proper work of extension interface/class code generation
     *
     * @return \Magento\TwoFactorAuth\Api\Data\GoogleConfigureExtensionInterface|null
     */
    public function getExtensionAttributes(): ?GoogleConfigureExtensionInterface
    {
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * Set an extension attributes object
     *
     * @param \Magento\TwoFactorAuth\Api\Data\GoogleConfigureExtensionInterface $extensionAttributes
     */
    public function setExtensionAttributes(GoogleConfigureExtensionInterface $extensionAttributes): void
    {
        $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}
