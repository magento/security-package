<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaFrontendUi\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Phrase;
use Magento\ReCaptcha\Model\ConfigInterface as ReCaptchaConfig;
use Magento\Store\Model\ScopeInterface;

/**
 * @inheritdoc
 */
class FrontendConfig implements FrontendConfigInterface
{
    private const XML_PATH_ENABLED_FRONTEND = 'recaptcha/frontend/enabled';
    private const XML_PATH_MIN_SCORE = 'recaptcha/frontend/min_score';
    private const XML_PATH_SIZE = 'recaptcha/frontend/size';
    private const XML_PATH_THEME = 'recaptcha/frontend/theme';
    private const XML_PATH_POSITION = 'recaptcha/frontend/position';
    private const XML_PATH_LANGUAGE_CODE = 'recaptcha/frontend/lang';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var ReCaptchaConfig
     */
    private $reCaptchaConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param ReCaptchaConfig $reCaptchaConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig, ReCaptchaConfig $reCaptchaConfig)
    {
        $this->scopeConfig = $scopeConfig;
        $this->reCaptchaConfig = $reCaptchaConfig;
    }

    /**
     * @inheritdoc
     */
    public function isFrontendEnabled(): bool
    {
        if (!$this->reCaptchaConfig->getPrivateKey() || !$this->reCaptchaConfig->getPublicKey()) {
            return false;
        }

        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_ENABLED_FRONTEND,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * @inheritdoc
     */
    public function getSize(): string
    {
        if ($this->reCaptchaConfig->isInvisibleRecaptcha()) {
            return 'invisible';
        }

        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_SIZE,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * @inheritdoc
     */
    public function getTheme(): ?string
    {
        if ($this->reCaptchaConfig->isInvisibleRecaptcha()) {
            return null;
        }

        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_THEME,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * Get language code
     * @return string
     */
    public function getLanguageCode(): string
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_LANGUAGE_CODE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @inheritdoc
     */
    public function getPosition(): ?string
    {
        if (!$this->reCaptchaConfig->isInvisibleRecaptcha()) {
            return null;
        }

        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_POSITION,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * @inheritdoc
     */
    public function getMinScore(): float
    {
        return min(1.0, max(0.1, (float)$this->scopeConfig->getValue(
            self::XML_PATH_MIN_SCORE,
            ScopeInterface::SCOPE_WEBSITE
        )));
    }

    /**
     * @inheritdoc
     */
    public function getErrorMessage(): Phrase
    {
        if ($this->reCaptchaConfig->getType() === 'recaptcha_v3') {
            return __('You cannot proceed with such operation, your reCaptcha reputation is too low.');
        }

        return __('Incorrect ReCaptcha validation');
    }
}
