<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ReCaptcha\Model;

use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Phrase;
use Magento\Store\Model\ScopeInterface;

/**
 * Read configuration from store config
 */
class Config implements ConfigInterface
{
    public const XML_PATH_ENABLED_BACKEND = 'recaptcha/backend/enabled';
    public const XML_PATH_ENABLED_FRONTEND = 'recaptcha/frontend/enabled';

    public const XML_PATH_TYPE = 'recaptcha/general/type';
    public const XML_PATH_LANGUAGE_CODE = 'recaptcha/frontend/lang';

    public const XML_PATH_POSITION_FRONTEND = 'recaptcha/frontend/position';

    public const XML_PATH_SIZE_MIN_SCORE_BACKEND = 'recaptcha/backend/min_score';
    public const XML_PATH_SIZE_MIN_SCORE_FRONTEND = 'recaptcha/frontend/min_score';
    public const XML_PATH_SIZE_BACKEND = 'recaptcha/backend/size';
    public const XML_PATH_SIZE_FRONTEND = 'recaptcha/frontend/size';
    public const XML_PATH_THEME_BACKEND = 'recaptcha/backend/theme';
    public const XML_PATH_THEME_FRONTEND = 'recaptcha/frontend/theme';

    public const XML_PATH_PUBLIC_KEY = 'recaptcha/general/public_key';
    public const XML_PATH_PRIVATE_KEY = 'recaptcha/general/private_key';

    public const XML_PATH_ENABLED_FRONTEND_LOGIN = 'recaptcha/frontend/enabled_login';
    public const XML_PATH_ENABLED_FRONTEND_FORGOT = 'recaptcha/frontend/enabled_forgot';
    public const XML_PATH_ENABLED_FRONTEND_CONTACT = 'recaptcha/frontend/enabled_contact';
    public const XML_PATH_ENABLED_FRONTEND_CREATE = 'recaptcha/frontend/enabled_create';
    public const XML_PATH_ENABLED_FRONTEND_NEWSLETTER = 'recaptcha/frontend/enabled_newsletter';

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
        if (!$this->isAreaEnabled(Area::AREA_ADMINHTML) || !$this->getPrivateKey() || !$this->getPublicKey()) {
            return false;
        }

        return (bool) $this->scopeConfig->getValue(static::XML_PATH_ENABLED_BACKEND);
    }

    /**
     * Return true if enabled on frontend
     * @return bool
     */
    public function isEnabledFrontend(): bool
    {
        if (!$this->isAreaEnabled(Area::AREA_FRONTEND) || !$this->getPrivateKey() || !$this->getPublicKey()) {
            return false;
        }

        return (bool) $this->scopeConfig->getValue(
            static::XML_PATH_ENABLED_FRONTEND,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * Return true if enabled on frontend login
     * @return bool
     */
    public function isEnabledFrontendLogin(): bool
    {
        if (!$this->isEnabledFrontend()) {
            return false;
        }

        return (bool) $this->scopeConfig->getValue(
            static::XML_PATH_ENABLED_FRONTEND_LOGIN,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * Return true if enabled on frontend forgot password
     * @return bool
     */
    public function isEnabledFrontendForgot(): bool
    {
        if (!$this->isEnabledFrontend()) {
            return false;
        }

        return (bool) $this->scopeConfig->getValue(
            static::XML_PATH_ENABLED_FRONTEND_FORGOT,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * Return true if enabled on frontend contact
     * @return bool
     */
    public function isEnabledFrontendContact(): bool
    {
        if (!$this->isEnabledFrontend()) {
            return false;
        }

        return (bool) $this->scopeConfig->getValue(
            static::XML_PATH_ENABLED_FRONTEND_CONTACT,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * Return true if enabled on frontend create user
     * @return bool
     */
    public function isEnabledFrontendCreateUser(): bool
    {
        if (!$this->isEnabledFrontend()) {
            return false;
        }

        return (bool) $this->scopeConfig->getValue(
            static::XML_PATH_ENABLED_FRONTEND_CREATE,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * Return true if enabled on frontend newsletter
     * @return bool
     */
    public function isEnabledFrontendNewsletter(): bool
    {
        if (!$this->isEnabledFrontend() || !$this->isInvisibleRecaptcha()) {
            return false;
        }

        return (bool) $this->scopeConfig->getValue(
            static::XML_PATH_ENABLED_FRONTEND_NEWSLETTER,
            ScopeInterface::SCOPE_WEBSITE
        );
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
    public function getFrontendSize(): string
    {
        if ($this->isInvisibleRecaptcha()) {
            return 'invisible';
        }

        return (string) $this->scopeConfig->getValue(
            static::XML_PATH_SIZE_FRONTEND,
            ScopeInterface::SCOPE_WEBSITE
        );
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
    public function getFrontendTheme(): ?string
    {
        if ($this->isInvisibleRecaptcha()) {
            return null;
        }

        return (string) $this->scopeConfig->getValue(
            static::XML_PATH_THEME_FRONTEND,
            ScopeInterface::SCOPE_WEBSITE
        );
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
     * Get data size
     * @return string
     */
    public function getFrontendPosition(): ?string
    {
        if (!$this->isInvisibleRecaptcha()) {
            return null;
        }

        return (string) $this->scopeConfig->getValue(
            static::XML_PATH_POSITION_FRONTEND,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * Get frontend type
     * @return string
     * @deprecated since 1.6.0
     * @see getType
     */
    public function getFrontendType()
    {
        return (string) $this->scopeConfig->getValue(static::XML_PATH_TYPE);
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
     * Get language code
     * @return string
     */
    public function getLanguageCode(): string
    {
        return (string) $this->scopeConfig->getValue(
            static::XML_PATH_LANGUAGE_CODE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get minimum frontend score
     * @return float
     */
    public function getMinFrontendScore(): float
    {
        return min(1.0, max(0.1, (float) $this->scopeConfig->getValue(
            static::XML_PATH_SIZE_MIN_SCORE_FRONTEND,
            ScopeInterface::SCOPE_WEBSITE
        )));
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

    /**
     * Return true if area is configured to be active
     * @param string $area
     * @return bool
     */
    public function isAreaEnabled(string $area): bool
    {
        if (!in_array($area, [Area::AREA_FRONTEND, Area::AREA_ADMINHTML], true)) {
            throw new \InvalidArgumentException('Area parameter must be one of frontend or adminhtml');
        }

        return (($area === Area::AREA_ADMINHTML) && $this->isEnabledBackend())
            || (($area === Area::AREA_FRONTEND) && $this->isEnabledFrontend());
    }
}
