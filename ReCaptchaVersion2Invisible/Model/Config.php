<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */

declare(strict_types=1);

namespace Magento\ReCaptchaVersion2Invisible\Model;

use Magento\Framework\ObjectManager\ResetAfterRequestInterface;
use Magento\ReCaptchaVersion2Invisible\Model\Frontend\UiConfigProvider;
use Magento\ReCaptchaUi\Model\ReCaptchaConfigInterface;

class Config implements ReCaptchaConfigInterface, ResetAfterRequestInterface
{
    /**
     * @var array
     */
    private array $uiConfig = [];

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
        return $this->getUiConfig()['rendering']['sitekey'] ?? '';
    }

    /**
     * ReCaptcha v2 Invisible does not provide configuration for minimum score
     *
     * @return null|float
     */
    public function getMinimumScore(): ?float
    {
        return null;
    }

    /**
     * Get configured captcha's badge position
     *
     * @return string
     */
    public function getBadgePosition(): string
    {
        return $this->getUiConfig()['rendering']['badge'] ?? '';
    }

    /**
     * Get configured captcha's theme
     *
     * @return string
     */
    public function getTheme(): string
    {
        return $this->getUiConfig()['rendering']['theme'] ?? '';
    }

    /**
     * Get code of language to send notifications
     *
     * @return string
     */
    public function getLanguageCode(): string
    {
        return $this->getUiConfig()['rendering']['hl'] ?? '';
    }

    /**
     * Get front-end's UI configurations
     *
     * @return array
     */
    private function getUiConfig(): array
    {
        if (empty($this->uiConfig)) {
            $this->uiConfig = $this->uiConfigProvider->get() ?? [];
        }
        return $this->uiConfig;
    }

    /**
     * @inheritDoc
     */
    public function _resetState(): void
    {
        $this->uiConfig = [];
    }
}
