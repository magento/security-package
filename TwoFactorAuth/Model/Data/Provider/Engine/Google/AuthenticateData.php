<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model\Data\Provider\Engine\Google;

use Magento\Framework\Api\AbstractExtensibleModel;
use Magento\TwoFactorAuth\Api\Data\GoogleAuthenticateExtensionInterface;
use Magento\TwoFactorAuth\Api\Data\GoogleAuthenticateInterface;

/**
 * Represents google authentication data
 */
class AuthenticateData extends AbstractExtensibleModel implements GoogleAuthenticateInterface
{
    /**
     * @inheritDoc
     */
    public function getOtp(): string
    {
        return (string)$this->_get(self::OTP);
    }

    /**
     * @inheritDoc
     */
    public function setOtp(string $value): void
    {
        $this->setData(self::OTP, $value);
    }

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * Used fully qualified namespaces in annotations for proper work of extension interface/class code generation
     *
     * @return \Magento\TwoFactorAuth\Api\Data\GoogleAuthenticateExtensionInterface|null
     */
    public function getExtensionAttributes(): ?GoogleAuthenticateExtensionInterface
    {
        return $this->_get(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * Set an extension attributes object
     *
     * @param \Magento\TwoFactorAuth\Api\Data\GoogleAuthenticateExtensionInterface $extensionAttributes
     */
    public function setExtensionAttributes(GoogleAuthenticateExtensionInterface $extensionAttributes): void
    {
        $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}
