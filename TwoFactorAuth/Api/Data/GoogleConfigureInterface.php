<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\TwoFactorAuth\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Google configuration data interface
 */
interface GoogleConfigureInterface extends ExtensibleDataInterface
{
    /**
     * QR Code URL field name
     */
    public const QR_CODE_URL = 'qr_code_url';

    /**
     * Secret code field name
     */
    public const SECRET_CODE = 'secret_code';

    /**
     * Get value for qr code url
     *
     * @return string
     */
    public function getQrCodeUrl(): string;

    /**
     * Set value for qr code url
     *
     * @param string $value
     * @return void
     */
    public function setQrCodeUrl(string $value): void;

    /**
     * Get value for secret code
     *
     * @return string
     */
    public function getSecretCode(): string;

    /**
     * Set value for secret code
     *
     * @param string $value
     * @return void
     */
    public function setSecretCode(string $value): void;

    /**
     * Retrieve existing extension attributes object or create a new one
     *
     * Used fully qualified namespaces in annotations for proper work of extension interface/class code generation
     *
     * @return \Magento\TwoFactorAuth\Api\Data\GoogleConfigureExtensionInterface|null
     */
    public function getExtensionAttributes(): ?GoogleConfigureExtensionInterface;

    /**
     * Set an extension attributes object
     *
     * @param \Magento\TwoFactorAuth\Api\Data\GoogleConfigureExtensionInterface $extensionAttributes
     * @return void
     */
    public function setExtensionAttributes(
        GoogleConfigureExtensionInterface $extensionAttributes
    ): void;
}
