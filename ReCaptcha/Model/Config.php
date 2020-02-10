<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptcha\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Phrase;
use Magento\Store\Model\ScopeInterface;

/**
 * Read configuration from store config
 */
class Config implements ConfigInterface
{
    public const XML_PATH_TYPE = 'recaptcha/general/type';
    public const XML_PATH_PUBLIC_KEY = 'recaptcha/general/public_key';
    public const XML_PATH_PRIVATE_KEY = 'recaptcha/general/private_key';

    public const XML_PATH_ENABLED_BACKEND = 'recaptcha/backend/enabled';
    public const XML_PATH_SIZE_MIN_SCORE_BACKEND = 'recaptcha/backend/min_score';
    public const XML_PATH_SIZE_BACKEND = 'recaptcha/backend/size';
    public const XML_PATH_THEME_BACKEND = 'recaptcha/backend/theme';

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
     * Get error
     * @return Phrase
     */
    public function getErrorDescription(): Phrase
    {
        if ($this->getType() === 'recaptcha_v3') {
            return __('You cannot proceed with such operation, your reCaptcha reputation is too low.');
        }

        return __('Incorrect ReCaptcha validation');
    }

    /**
     * Get google recaptcha public key
     * @return string
     */
    public function getPublicKey(): string
    {
        return trim((string) $this->scopeConfig->getValue(static::XML_PATH_PUBLIC_KEY, ScopeInterface::SCOPE_WEBSITE));
    }

    /**
     * Get google recaptcha private key
     * @return string
     */
    public function getPrivateKey(): string
    {
        return trim((string) $this->scopeConfig->getValue(static::XML_PATH_PRIVATE_KEY, ScopeInterface::SCOPE_WEBSITE));
    }

    /**
     * Return true if enabled on backend
     * @return bool
     */
    public function isEnabledBackend(): bool
    {
        if (!$this->getPrivateKey() || !$this->getPublicKey()) {
            return false;
        }

        return (bool) $this->scopeConfig->getValue(static::XML_PATH_ENABLED_BACKEND);
    }

    /**
     * @return bool
     */
    public function isInvisibleRecaptcha(): bool
    {
        return in_array($this->getType(), ['invisible', 'recaptcha_v3'], true);
    }

    /**
     * Get data size
     * @return string
     */
    public function getBackendSize(): string
    {
        if ($this->isInvisibleRecaptcha()) {
            return 'invisible';
        }

        return (string) $this->scopeConfig->getValue(static::XML_PATH_SIZE_BACKEND);
    }

    /**
     * Get data size
     * @return string
     */
    public function getBackendTheme(): string
    {
        return (string) $this->scopeConfig->getValue(static::XML_PATH_THEME_BACKEND);
    }

    /**
     * Get reCaptcha type
     * @return string
     */
    public function getType(): string
    {
        return (string) $this->scopeConfig->getValue(static::XML_PATH_TYPE);
    }

    /**
     * Get minimum frontend score
     * @return float
     */
    public function getMinBackendScore(): float
    {
        return min(1.0, max(0.1, (float) $this->scopeConfig->getValue(
            static::XML_PATH_SIZE_MIN_SCORE_BACKEND
        )));
    }
}
