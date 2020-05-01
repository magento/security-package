<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model\Data\Provider\Engine\U2fkey;

use Magento\Framework\Model\AbstractExtensibleModel;
use Magento\TwoFactorAuth\Api\Data\U2fWebAuthnRequestExtensionInterface;
use Magento\TwoFactorAuth\Api\Data\U2fWebAuthnRequestInterface;

/**
 * WebAuthn data
 */
class WebAuthnRequest extends AbstractExtensibleModel implements U2fWebAuthnRequestInterface
{
    /**
     * @inheritDoc
     */
    public function getCredentialRequestOptionsJson(): string
    {
        return (string)$this->getData(self::CREDENTIAL_REQUEST_OPTIONS_JSON);
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
    public function getExtensionAttributes(): ?U2fWebAuthnRequestExtensionInterface
    {
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * @inheritDoc
     */
    public function setExtensionAttributes(U2fWebAuthnRequestExtensionInterface $extensionAttributes): void
    {
        $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}
