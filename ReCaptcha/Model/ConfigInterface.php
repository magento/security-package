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
     * @return bool
     */
    public function isInvisibleRecaptcha(): bool;

    /**
     * Get reCaptcha type
     * @return string
     */
    public function getType(): string;
}
