<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaApi\Api\Data;

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
     * Get reCAPTCHA Type
     *
     * @return string
     */
    public function getCaptchaType(): string;

    /**
     * Get Minimum Score Threshold
     *
     * Applicable only to "reCAPTCHA v3" type
     *
     * @return float|null
     */
    public function getScoreThreshold(): ?float;

    /**
     * Get Remote IP Address (IPv4 string)
     *
     * @return string
     */
    public function getRemoteIp(): string;

    /**
     * Get extension attributes object
     *
     * Used fully qualified namespaces in annotations for proper work of extension interface/class code generation
     *
     * @return \Magento\ReCaptchaApi\Api\Data\ValidationConfigExtensionInterface|null
     */
    public function getExtensionAttributes(): ?ValidationConfigExtensionInterface;
}
