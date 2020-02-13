<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaApi\Api;

use Magento\Framework\Phrase;

/**
 * Represents reCAPTCHA base configuration
 *
 * @api
 */
interface CaptchaConfigInterface
{
    /**
     * Get Google API Website Key
     *
     * @return string
     */
    public function getPublicKey(): string;

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
     * From 0.0 to 1.0, where 0.0 is absolutely a robot and 1.0 is a human.
     *
     * @return float
     */
    public function getScoreThreshold(): float;

    /**
     * Get Invisible Badge Position
     *
     * Applicable only to Invisible reCAPTCHA types
     *
     * @return string
     */
    public function getInvisibleBadgePosition(): string;

    /**
     * Get theme
     *
     * Applicable only for visible captcha type (for example "reCAPTCHA v2")
     *
     * @return string
     */
    public function getTheme(): string;

    /**
     * Get size
     *
     * Applicable only for visible captcha type (for example "reCAPTCHA v2")
     *
     * @return string
     */
    public function getSize(): string;

    /**
     * Get language code
     *
     * Applicable only for visible captcha type (for example "reCAPTCHA v2")
     *
     * @return string
     */
    public function getLanguageCode(): string;

    /**
     * Sugar method. Return true if reCAPTCHA keys (public and private) are configured
     *
     * @return bool
     */
    public function areKeysConfigured(): bool;

    /**
     * Get error message
     *
     * @return Phrase
     */
    public function getErrorMessage(): Phrase;

    /**
     * Return true if reCAPTCHA is enabled for specific functionality
     *
     * @param string $key
     * @return bool
     */
    public function isCaptchaEnabledFor(string $key): bool;
}
