<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaVersion3Invisible\Model;

use Magento\Framework\ObjectManager\ResetAfterRequestInterface;
use Magento\ReCaptchaValidationApi\Api\Data\ValidationConfigInterface;
use Magento\ReCaptchaVersion3Invisible\Model\Frontend\UiConfigProvider;
use Magento\ReCaptchaVersion3Invisible\Model\Frontend\ValidationConfigProvider;

class Config implements ResetAfterRequestInterface
{
    /**
     * @var string|null
     */
    private ?string $websiteKey = null;

    /**
     * @var float|null
     */
    private ?float $minimumScore = null;

    /**
     * @var string|null
     */
    private ?string $badgePosition = null;

    /**
     * @var string|null
     */
    private ?string $languageCode = null;

    /**
     * @var UiConfigProvider
     */
    private UiConfigProvider $uiConfigProvider;

    /**
     * @var array
     */
    private array $uiConfig = [];

    /**
     * @var ValidationConfigProvider
     */
    private ValidationConfigProvider $validationConfigProvider;

    /**
     * @var ValidationConfigInterface|null
     */
    private ?ValidationConfigInterface $validationConfig = null;

    /**
     * @var array
     */
    private array $formTypes;

    /**
     * @param UiConfigProvider $uiConfigProvider
     * @param ValidationConfigProvider $validationConfigProvider
     * @param array $formTypes
     */
    public function __construct(
        UiConfigProvider $uiConfigProvider,
        ValidationConfigProvider $validationConfigProvider,
        array $formTypes = []
    ) {
        $this->formTypes = $formTypes;
        $this->uiConfigProvider = $uiConfigProvider;
        $this->validationConfigProvider = $validationConfigProvider;
    }

    /**
     * Get website's Google API public key
     *
     * @return string
     */
    public function getWebsiteKey(): string
    {
        if (!$this->websiteKey) {
            $this->websiteKey = $this->getUiConfig()['rendering']['sitekey'];
        }
        return $this->websiteKey;
    }

    /**
     * Get configured minimum score value
     *
     * @return float
     */
    public function getMinimumScore(): float
    {
        if (!$this->minimumScore) {
            $this->minimumScore = $this->validationConfig->getExtensionAttributes()->getScoreThreshold();
        }
        return $this->minimumScore;
    }

    /**
     * Get configured captcha's badge position
     *
     * @return string
     */
    public function getBadgePosition(): string
    {
        if (!$this->badgePosition) {
            $this->badgePosition = $this->getUiConfig()['rendering']['badge'];
        }
        return $this->badgePosition;
    }

    /**
     * Get code of language to send notifications
     *
     * @return string
     */
    public function getLanguageCode(): string
    {
        if (!$this->languageCode) {
            $this->languageCode = $this->getUiConfig()['rendering']['hl'];
        }
        return $this->languageCode;
    }

    /**
     * Get ReCaptchaV3's available form types
     *
     * @return array
     */
    public function getFormTypes(): array
    {
        return $this->formTypes;
    }

    /**
     * Get front-end's validation configurations
     *
     * @return ValidationConfigInterface
     */
    public function getValidationConfig(): ValidationConfigInterface
    {
        if (!$this->validationConfig) {
            $this->validationConfig = $this->validationConfigProvider->get();
        }
        return $this->validationConfig;
    }

    /**
     * Get front-end's UI configurations
     *
     * @return array
     */
    private function getUiConfig(): array
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
        $this->websiteKey = null;
        $this->uiConfig = [];
        $this->validationConfig = null;
    }
}
