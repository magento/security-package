<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaAdminUi\Model;

use Magento\Framework\Phrase;

/**
 * Represents backend ReCaptcha configuration
 *
 * @api
 */
interface AdminConfigInterface
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
     * Get score threshold score
     * @return float|null
     */
    public function getScoreThreshold(): ?float;

    /**
     * Get error message
     * @return Phrase
     */
    public function getErrorMessage(): Phrase;
}
