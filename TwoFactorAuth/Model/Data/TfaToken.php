<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model\Data;

use Magento\Framework\Model\AbstractExtensibleModel;
use Magento\TwoFactorAuth\Api\Data\TfaTokenExtensionInterface;
use Magento\TwoFactorAuth\Api\Data\TfaTokenInterface;

/**
 * Represents two-factor token needed to make 2fa changes
 */
class TfaToken extends AbstractExtensibleModel implements TfaTokenInterface
{
    /**
     * @inheritDoc
     */
    public function getToken(): string
    {
        return $this->_getData(static::TOKEN);
    }

    /**
     * @inheritDoc
     */
    public function setToken(string $value): void
    {
        $this->setData(static::TOKEN, $value);
    }

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * Used fully qualified namespaces in annotations for proper work of extension interface/class code generation
     *
     * @return \Magento\TwoFactorAuth\Api\Data\TfaTokenExtensionInterface|null
     */
    public function getExtensionAttributes(): ?TfaTokenExtensionInterface
    {
        return $this->_getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * Set an extension attributes object
     *
     * @param \Magento\TwoFactorAuth\Api\Data\TfaTokenExtensionInterface $extensionAttributes
     */
    public function setExtensionAttributes(TfaTokenExtensionInterface $extensionAttributes): void
    {
        $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}
