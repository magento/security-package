<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaValidationApi\Api\Data;

/**
 * Represents reCAPTCHA validation configuration
 *
 * @api
 */
interface ValidationConfigInterface
{
    /**
     * Get Google API Secret Key
     *
     * @return string
     */
    public function getPrivateKey(): string;

    /**
     * Get Remote IP Address (IPv4 string)
     *
     * @return string
     */
    public function getRemoteIp(): string;

    /**
     * Get validation failure message
     *
     * @return string
     */
    public function getValidationFailureMessage(): string;

    /**
     * Get extension attributes object
     *
     * Used fully qualified namespaces in annotations for proper work of extension interface/class code generation
     *
     * @return \Magento\ReCaptchaValidationApi\Api\Data\ValidationConfigExtensionInterface|null
     */
    public function getExtensionAttributes(): ?ValidationConfigExtensionInterface;
}
