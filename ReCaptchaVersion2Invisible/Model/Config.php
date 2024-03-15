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

namespace Magento\ReCaptchaVersion2Invisible\Model;

use Magento\Framework\ObjectManager\ResetAfterRequestInterface;
use Magento\ReCaptchaVersion2Invisible\Model\Frontend\UiConfigProvider;
use Magento\ReCaptchaWebapiGraphQl\Model\Adapter\ReCaptchaConfigInterface;

class Config implements ReCaptchaConfigInterface, ResetAfterRequestInterface
{
    /**
     * @var array
     */
    private array $uiConfig = [];

    /**
     * @var $minimumScore
     */
    private ?float $minimumScore = null;

    /**
     * @param UiConfigProvider $uiConfigProvider
     */
    public function __construct(
        private readonly UiConfigProvider $uiConfigProvider,
    ) {
    }

    /**
     * Get website's Google API public key
     *
     * @return string
     */
    public function getWebsiteKey(): string
    {
        return $this->getUiConfig()['rendering']['sitekey'];
    }

    /**
     * ReCaptcha V2 Invisible does not provide configurable minimum score setting
     *
     * @return null
     */
    public function getMinimumScore()
    {
        return $this->minimumScore;
    }

    /**
     * Get configured captcha's badge position
     *
     * @return string
     */
    public function getBadgePosition(): string
    {
        return $this->getUiConfig()['rendering']['badge'];
    }

    /**
     * Get configured captcha's theme
     *
     * @return string
     */
    public function getTheme(): string
    {
        return $this->getUiConfig()['rendering']['theme'];
    }

    /**
     * Get code of language to send notifications
     *
     * @return string
     */
    public function getLanguageCode(): string
    {
        return $this->getUiConfig()['rendering']['hl'];
    }

    /**
     * Get front-end's UI configurations
     *
     * @return array
     */
    public function getUiConfig(): array
    {
        if (empty($this->uiConfig)) {
            $this->uiConfig = $this->uiConfigProvider->get();
        }
        return $this->uiConfig;
    }

    /**
     * @inheritDoc
     */
    public function _resetState(): void
    {
        $this->uiConfig = [];
        $this->minimumScore = null;
    }
}
