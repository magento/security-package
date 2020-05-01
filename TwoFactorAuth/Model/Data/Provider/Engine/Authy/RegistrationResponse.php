<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\TwoFactorAuth\Model\Data\Provider\Engine\Authy;

use Magento\Framework\Model\AbstractExtensibleModel;
use Magento\TwoFactorAuth\Api\Data\AuthyRegistrationPromptResponseInterface as ResponseInterface;
use Magento\TwoFactorAuth\Api\Data\AuthyRegistrationPromptResponseExtensionInterface;

/**
 * Represents authy authentication data
 */
class RegistrationResponse extends AbstractExtensibleModel implements ResponseInterface
{
    /**
     * @inheritDoc
     */
    public function getMessage(): string
    {
        return (string)$this->getData(self::MESSAGE);
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
    public function getExpirationSeconds(): int
    {
        return (int)$this->getData(self::EXPIRATION_SECONDS);
    }

    /**
     * @inheritDoc
     */
    public function setExpirationSeconds(int $value): void
    {
        $this->setData(self::EXPIRATION_SECONDS, $value);
    }

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * Used fully qualified namespaces in annotations for proper work of extension interface/class code generation
     *
     * @return \Magento\TwoFactorAuth\Api\Data\AuthyRegistrationPromptResponseExtensionInterface|null
     */
    public function getExtensionAttributes(): ?AuthyRegistrationPromptResponseExtensionInterface
    {
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * Set an extension attributes object
     *
     * @param \Magento\TwoFactorAuth\Api\Data\AuthyRegistrationPromptResponseExtensionInterface $extensionAttributes
     */
    public function setExtensionAttributes(
        AuthyRegistrationPromptResponseExtensionInterface $extensionAttributes
    ): void {
        $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}
