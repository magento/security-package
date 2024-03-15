<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 *
 * NOTICE: All information contained herein is, and remains
 * the property of Adobe and its suppliers, if any. The intellectual
 * and technical concepts contained herein are proprietary to Adobe
 * and its suppliers and are protected by all applicable intellectual
 * property laws, including trade secret and copyright laws.
 * Dissemination of this information or reproduction of this material
 * is strictly forbidden unless prior written permission is obtained from
 * Adobe.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaWebapiGraphQl\Model\Adapter;

/**
 * Interface for ReCaptcha config adapters. Used in Config adapters which retrieve
 * configuration settings for different ReCaptcha types.
 */
interface ReCaptchaConfigInterface
{
    /**
     * Get front-end's UI configurations
     *
     * @return array
     */
    public function getUiConfig();

    /**
     * Get website's Google API public key
     *
     * @return string
     */
    public function getWebsiteKey();
    
    /**
     * Get configured captcha's theme
     *
     * @return string
     */
    public function getTheme();

    /**
     * Get code of language to send notifications
     *
     * @return string
     */
    public function getLanguageCode();

    /**
     * Returns minimum score setting
     *
     * @return mixed
     */
    public function getMinimumScore();

    /**
     * Returns badge_position setting
     *
     * @return string
     */
    public function getBadgePosition();
}
