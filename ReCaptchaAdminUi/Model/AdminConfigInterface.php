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
     * Return true if enabled on backend
     * @return bool
     */
    public function isBackendEnabled(): bool;

    /**
     * Get size
     * @return string
     */
    public function getSize(): string;

    /**
     * Get theme
     * @return string
     */
    public function getTheme(): string;

    /**
     * Get minimum frontend score
     * @return float
     */
    public function getMinScore(): float;

    /**
     * Get error message
     * @return Phrase
     */
    public function getErrorMessage(): Phrase;
}
