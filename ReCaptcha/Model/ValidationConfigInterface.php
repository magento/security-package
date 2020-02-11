<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptcha\Model;

/**
 * Represents ReCaptcha validation configuration
 *
 * @api
 */
interface ValidationConfigInterface
{
    /**
     * Get google recaptcha private key
     *
     * @return string
     */
    public function getPrivateKey(): string;

    /**
     * Get reCaptcha type
     *
     * @return string
     */
    public function getCaptchaType(): string;

    /**
     * Get score threshold (applicable only for recaptcha_v3)
     *
     * @return float|null
     */
    public function getScoreThreshold(): ?float;

    /**
     * Get remote IP address (IPv4 string)
     *
     * @return string
     */
    public function getRemoteIp(): string;
}
