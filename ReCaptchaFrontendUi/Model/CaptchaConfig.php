<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptchaFrontendUi\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\ReCaptchaApi\Api\CaptchaConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * @inheritdoc
 */
class CaptchaConfig implements CaptchaConfigInterface
{
    private const XML_PATH_TYPE = 'recaptcha/frontend/type';
    private const XML_PATH_PUBLIC_KEY = 'recaptcha/frontend/public_key';
    private const XML_PATH_PRIVATE_KEY = 'recaptcha/frontend/private_key';

    private const XML_PATH_SCORE_THRESHOLD = 'recaptcha/frontend/score_threshold';
    private const XML_PATH_SIZE = 'recaptcha/frontend/size';
    private const XML_PATH_THEME = 'recaptcha/frontend/theme';
    private const XML_PATH_POSITION = 'recaptcha/frontend/position';
    private const XML_PATH_LANGUAGE_CODE = 'recaptcha/frontend/lang';

    private const XML_PATH_IS_ENABLED_FOR = 'recaptcha/frontend/enabled_for_';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @inheritdoc
     */
    public function getPublicKey(): string
    {
        return trim((string)$this->scopeConfig->getValue(self::XML_PATH_PUBLIC_KEY, ScopeInterface::SCOPE_WEBSITE));
    }

    /**
     * @inheritdoc
     */
    public function getPrivateKey(): string
    {
        return trim((string)$this->scopeConfig->getValue(self::XML_PATH_PRIVATE_KEY, ScopeInterface::SCOPE_WEBSITE));
    }

    /**
     * @inheritdoc
     */
    public function isInvisibleRecaptcha(): bool
    {
        return in_array($this->getCaptchaType(), ['invisible', 'recaptcha_v3'], true);
    }

    /**
     * @inheritdoc
     */
    public function getCaptchaType(): string
    {
        return (string)$this->scopeConfig->getValue(self::XML_PATH_TYPE);
    }

    /**
     * @inheritdoc
     */
    public function areKeysConfigured(): bool
    {
        return $this->getPrivateKey() && $this->getPublicKey();
    }

    /**
     * @inheritdoc
     */
    public function getSize(): string
    {
        if ($this->isInvisibleRecaptcha()) {
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
        if ($this->isInvisibleRecaptcha()) {
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
        if (!$this->isInvisibleRecaptcha()) {
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
    public function getScoreThreshold(): float
    {
        return min(1.0, max(0.1, (float)$this->scopeConfig->getValue(
            self::XML_PATH_SCORE_THRESHOLD,
            ScopeInterface::SCOPE_WEBSITE
        )));
    }

    /**
     * @inheritdoc
     */
    public function getErrorMessage(): Phrase
    {
        if ($this->getCaptchaType() === 'recaptcha_v3') {
            return __('You cannot proceed with such operation, your reCaptcha reputation is too low.');
        }

        return __('Incorrect ReCaptcha validation');
    }

    /**
     * @inheritdoc
     */
    public function isCaptchaEnabledFor(string $key): bool
    {
        if (!$this->areKeysConfigured()) {
            return false;
        }
        $flag = self::XML_PATH_IS_ENABLED_FOR . $key;

        if (false === $this->scopeConfig->isSetFlag($flag, ScopeInterface::SCOPE_WEBSITE)) {
            throw new LocalizedException(__('Captcha config with "%key" key does not exists.', ['key' => $key]));
        }
        return (bool)$this->scopeConfig->getValue($flag, ScopeInterface::SCOPE_WEBSITE);
    }
}
