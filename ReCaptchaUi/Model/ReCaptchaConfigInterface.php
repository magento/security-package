<?php
/**
 * Copyright 2026 Adobe
 * All Rights Reserved.
 */

declare(strict_types=1);

namespace Magento\ReCaptchaUi\Model;

/**
 * Interface for ReCaptcha config adapters. Used in Config adapters which retrieve
 * configuration settings for different ReCaptcha types.
 *
 * @api
 */
interface ReCaptchaConfigInterface
{
    /**
     * Get website's Google API public key
     *
     * @return string
     */
    public function getWebsiteKey(): string;

    /**
     * Get configured captcha's theme
     *
     * @return string
     */
    public function getTheme(): string;

    /**
     * Get code of language to send notifications
     *
     * @return string
     */
    public function getLanguageCode(): string;

    /**
     * Returns minimum score setting
     *
     * @return float|null
     */
    public function getMinimumScore(): ?float;

    /**
     * Returns badge_position setting
     *
     * @return string
     */
    public function getBadgePosition(): string;
}
