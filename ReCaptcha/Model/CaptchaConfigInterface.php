<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptcha\Model;

use Magento\Framework\Phrase;

/**
 * Represents base ReCaptcha configuration
 *
 * @api
 */
interface CaptchaConfigInterface
{
    /**
     * Get google recaptcha public key
     * @return string
     */
    public function getPublicKey(): string;

    /**
     * Get google recaptcha private key
     * @return string
     */
    public function getPrivateKey(): string;

    /**
     * Sugar method. Return true if captcha keys (public and private) are configured
     * @return bool
     */
    public function areKeysConfigured(): bool;

    /**
     * Get reCaptcha type
     * @return string
     */
    public function getCaptchaType(): string;

    /**
     * @return bool
     */
    public function isInvisibleRecaptcha(): bool;

    /**
     * Get size
     * @return string
     */
    public function getSize(): string;

    /**
     * Get theme
     * @return string
     */
    public function getTheme(): ?string;

    /**
     * Get minimum frontend score
     * @return float
     */
    public function getScoreThreshold(): float;

    /**
     * Get error message
     * @return Phrase
     */
    public function getErrorMessage(): Phrase;

    /**
     * Get language code
     * @return string
     */
    public function getLanguageCode(): string;

    /**
     * Get position
     * @return string
     */
    public function getPosition(): ?string;
}
