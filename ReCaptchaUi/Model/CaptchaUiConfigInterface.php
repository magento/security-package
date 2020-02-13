<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaUi\Model;

/**
 * Represents base ReCaptcha configuration
 *
 * @api
 */
interface CaptchaUiConfigInterface
{
    /**
     * Get google recaptcha public key
     * @return string
     */
    public function getPublicKey(): string;

    /**
     * Get theme
     * @return string
     */
    public function getTheme(): string;

    /**
     * Get size
     * @return string
     */
    public function getSize(): string;

    /**
     * Get position
     * @return string
     */
    public function getPosition(): string;

    /**
     * Get language code
     * @return string
     */
    public function getLanguageCode(): string;
}
