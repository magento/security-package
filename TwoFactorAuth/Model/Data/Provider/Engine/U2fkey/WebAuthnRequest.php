<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model\Data\Provider\Engine\U2fkey;

use Magento\Framework\Model\AbstractExtensibleModel;
use Magento\TwoFactorAuth\Api\Data\U2FWebAuthnRequestExtensionInterface;
use Magento\TwoFactorAuth\Api\Data\U2FWebAuthnRequestInterface;

/**
 * WebAuthn data
 */
class WebAuthnRequest extends AbstractExtensibleModel implements U2FWebAuthnRequestInterface
{
    /**
     * @inheritDoc
     */
    public function getCredentialRequestOptionsJson(): string
    {
        return (string)$this->_getData(self::CREDENTIAL_REQUEST_OPTIONS_JSON);
    }

    /**
     * @inheritDoc
     */
    public function setCredentialRequestOptionsJson(string $value): void
    {
        $this->setData(self::CREDENTIAL_REQUEST_OPTIONS_JSON, $value);
    }

    /**
     * @inheritDoc
     */
    public function getExtensionAttributes(): ?U2FWebAuthnRequestExtensionInterface
    {
        return $this->_getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * @inheritDoc
     */
    public function setExtensionAttributes(U2FWebAuthnRequestExtensionInterface $extensionAttributes): void
    {
        $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}
