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

    /**
     * Get reCAPTCHA type for specific functionality. Return NULL id reCAPTCHA is disabled for this functionality
     *
     * @param string $key
     * @return string|null
     */
    public function getCaptchaTypeFor(string $key): ?string;
}
