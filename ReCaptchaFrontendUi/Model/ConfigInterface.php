<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaFrontendUi\Model;

use Magento\Framework\Phrase;

/**
 * Represents general ReCaptcha configuration
 *
 * @api
 */
interface ConfigInterface
{
    /**
     * Return true if enabled on frontend
     * @return bool
     */
    public function isFrontendEnabled(): bool;

    /**
     * Get data size
     * @return string
     */
    public function getSize(): string;

    /**
     * Get data size
     * @return string
     */
    public function getTheme(): ?string;

    /**
     * Get language code
     * @return string
     */
    public function getLanguageCode(): string;

    /**
     * Get data size
     * @return string
     */
    public function getPosition(): ?string;

    /**
     * Get minimum frontend score
     * @return float
     */
    public function getMinScore(): float;

    /**
     * Get error
     * @return Phrase
     */
    public function getErrorDescription(): Phrase;
}
