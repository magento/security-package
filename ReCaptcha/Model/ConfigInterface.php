<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptcha\Model;

use Magento\Framework\Phrase;

/**
 * Represents general ReCaptcha configuration
 *
 * @api
 */
interface ConfigInterface
{
    /**
     * Get error
     * @return Phrase
     */
    public function getErrorDescription(): Phrase;

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
     * Return true if enabled on backend
     * @return bool
     */
    public function isEnabledBackend(): bool;

    /**
     * @return bool
     */
    public function isInvisibleRecaptcha(): bool;

    /**
     * Get data size
     * @return string
     */
    public function getBackendSize(): string;

    /**
     * Get data size
     * @return string
     */
    public function getBackendTheme(): string;

    /**
     * Get reCaptcha type
     * @return string
     */
    public function getType(): string;

    /**
     * Get minimum frontend score
     * @return float
     */
    public function getMinBackendScore(): float;
}
